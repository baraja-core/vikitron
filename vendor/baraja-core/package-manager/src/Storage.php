<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorException;
use Baraja\PackageManager\Exception\PackageEntityDoesNotExistsException;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Finder;

class Storage
{

	/**
	 * @var string
	 */
	private $basePath;

	public function __construct(string $basePath)
	{
		$this->basePath = $basePath;
	}

	/**
	 * @internal
	 * @return PackageDescriptorEntity
	 * @throws PackageEntityDoesNotExistsException
	 * @throws PackageDescriptorException
	 */
	public function load(): PackageDescriptorEntity
	{
		static $cache;

		if ($cache !== null) {
			return $cache;
		}

		if (trim($this->getPath()) !== '' && filesize($this->getPath()) > 1) {
			require_once $this->getPath();

			$cache = new \PackageDescriptorEntity();

			return $cache;
		}

		PackageEntityDoesNotExistsException::packageDescriptionEntityDoesNotExist();
	}

	/**
	 * @internal
	 * @param PackageDescriptorEntity $packageDescriptorEntity
	 * @param string $composerHash
	 * @throws PackageDescriptorException
	 */
	public function save(PackageDescriptorEntity $packageDescriptorEntity, string $composerHash = null): void
	{
		$class = new ClassType('PackageDescriptorEntity');

		$generatedDate = date('Y-m-d H:i:s');

		$class->setFinal()
			->setExtends(PackageDescriptorEntity::class)
			->addComment('This is temp class of PackageDescriptorEntity' . "\n")
			->addComment('@author Baraja PackageManager')
			->addComment('@generated ' . $generatedDate);

		$class->addConstant('GENERATED', time())
			->setVisibility('public')
			->addComment('@var string');

		$class->addMethod('getGeneratedDateTime')
			->setReturnType('string')
			->setBody('return \'' . $generatedDate . '\';');

		$class->addMethod('getGeneratedDateTimestamp')
			->setReturnType('int')
			->setBody('static $cache;'
				. "\n\n" . 'if ($cache !== null) {'
				. "\n\t" . 'return $cache;'
				. "\n" . '}'
				. "\n\n" . '$cache = strtotime($this->getGeneratedDateTime());'
				. "\n\n" . 'return $cache;');

		$class->addMethod('getComposerHash')
			->setReturnType('string')
			->setBody('return \'' . ($composerHash ?? '') . '\';');

		$reflection = new \ReflectionObject($packageDescriptorEntity);

		foreach ($reflection->getProperties() as $property) {
			if ($property->getName() === '__close') {
				$class->addProperty(
					$property->getName(),
					true
				)->setVisibility('protected');
			} else {
				$property->setAccessible(true);
				$class->addProperty(
					ltrim($property->getName(), '_'),
					$this->makeScalarValueOnly($property->getValue($packageDescriptorEntity))
				)->setVisibility('protected');
			}
		}

		if (!@file_put_contents($this->getPath(), '<?php' . "\n\n" . $class) && !is_file($this->getPath())) {
			PackageDescriptorException::tempFileGeneratingError($this->getPath());
		}
	}

	/**
	 * @internal
	 * Convert Nette SmartObject with private methods to Nette ArrayHash structure.
	 * While converting call getters, so you get only properties which you can get.
	 * Function supports recursive objects structure. Public properties will be included.
	 *
	 * @author Jan Barášek [2017-09-09]
	 * @param object|mixed|mixed[][] $input
	 * @return string[][]|mixed
	 */
	public function haystackToArray($input)
	{
		if (\is_object($input)) {
			try {
				$reflection = new \ReflectionClass($input);
			} catch (\ReflectionException $e) {
				return null;
			}

			$return = [];

			foreach ($input as $k => $v) {
				$return[$k] = $this->haystackToArray($v);
			}

			foreach ($reflection->getMethods() as $method) {
				if ($method->name !== 'getReflection' && preg_match('/^(get|is)(.+)$/', $method->name, $_method)) {
					$return[lcfirst($_method[2])] = $this->haystackToArray($input->{$method->name}());
				}
			}
		} elseif (\is_array($input)) {
			$return = [];
			foreach ($input as $k => $v) {
				$return[$k] = $this->haystackToArray($v);
			}
		} else {
			$return = $input;
		}

		return $return;
	}

	/**
	 * @param int $ttl
	 * @return string
	 * @throws PackageDescriptorException
	 */
	private function getPath(int $ttl = 3): string
	{
		static $cache;

		if ($cache !== null) {
			return $cache;
		}

		try {
			$dir = $this->basePath . '/_packageDescriptor';

			if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
				PackageDescriptorException::canNotCreateTempDir($dir);
			}

			$file = $dir . '/PackageDescriptorEntity.php';

			if (!is_file($file) && !@file_put_contents($file, '') && !is_file($file)) {
				PackageDescriptorException::canNotCreateTempFile($file);
			}

			$cache = $file;

			return $file;
		} catch (PackageDescriptorException $e) {
			if ($ttl > 0) {
				$this->tryFixTemp();

				return $this->getPath($ttl - 1);
			}

			throw $e;
		}
	}

	/**
	 * @return bool
	 */
	private function tryFixTemp(): bool
	{
		$basePath = $this->basePath . '/_packageDescriptor';
		/** @var string[] $finder */
		$finder = Finder::find('*')->in($basePath);

		foreach ($finder as $path => $value) {
			@unlink($path);
		}

		return @rmdir($basePath);
	}

	/**
	 * @param mixed|mixed[] $data
	 * @return mixed|mixed[]
	 */
	private function makeScalarValueOnly($data)
	{
		if (\is_array($data)) {
			$return = [];

			foreach ($data as $key => $value) {
				if (is_object($value) === false) {
					$return[$key] = $this->makeScalarValueOnly($value);
				} else {
					$return[$key] = (array) $value;
				}
			}

			return $return;
		}

		return $data;
	}

}
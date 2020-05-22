<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorException;
use Baraja\PackageManager\Exception\PackageEntityDoesNotExistsException;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Finder;

final class Storage
{

	/** @var string */
	private $basePath;


	public function __construct(string $basePath)
	{
		$this->basePath = $basePath;
	}


	/**
	 * @internal
	 * @return PackageDescriptorEntity
	 * @throws PackageEntityDoesNotExistsException|PackageDescriptorException
	 */
	public function load(): PackageDescriptorEntity
	{
		if (trim($path = $this->getPath()) === '' || filesize($path) < 10) {
			PackageEntityDoesNotExistsException::packageDescriptionEntityDoesNotExist();
		}

		require_once $path;

		if (\class_exists('\PackageDescriptorEntity') === false) {
			PackageEntityDoesNotExistsException::packageDescriptionEntityDoesNotExist();
		}

		return new \PackageDescriptorEntity();
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

		$class->setFinal()
			->setExtends(PackageDescriptorEntity::class)
			->addComment('This is temp class of PackageDescriptorEntity' . "\n")
			->addComment('@author Baraja PackageManager')
			->addComment('@generated ' . ($generatedDate = date('Y-m-d H:i:s')));

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
	 * @param mixed $input
	 * @return mixed
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

		if ($cache === null) {
			$dir = $this->basePath . '/cache/baraja/packageDescriptor';
			$cache = $dir . '/PackageDescriptorEntity.php';

			try {
				if (is_dir($dir) === false && !mkdir($dir, 0777, true) && !is_dir($dir)) {
					PackageDescriptorException::canNotCreateTempDir($dir);
				}

				if (is_file($cache) === false && !file_put_contents($cache, '') && !is_file($cache)) {
					PackageDescriptorException::canNotCreateTempFile($cache);
				}
			} catch (PackageDescriptorException $e) {
				if ($ttl > 0) {
					$this->tryFixTemp($dir);

					return $this->getPath($ttl - 1);
				}

				throw $e;
			}
		}

		return $cache;
	}


	/**
	 * @param string $basePath
	 * @return bool
	 */
	private function tryFixTemp(string $basePath): bool
	{
		foreach (Finder::find('*')->in($basePath) as $path => $value) {
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

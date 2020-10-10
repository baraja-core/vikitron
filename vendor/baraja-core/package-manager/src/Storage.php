<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorException;
use Baraja\PackageManager\Exception\PackageEntityDoesNotExistsException;
use Nette\IOException;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\FileSystem;
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


	public function save(PackageDescriptorEntity $packageDescriptorEntity, ?string $composerHash = null): void
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

		foreach ((new \ReflectionObject($packageDescriptorEntity))->getProperties() as $property) {
			if ($property->getName() === '__close') {
				$class->addProperty($property->getName(), true)->setVisibility('protected');
			} else {
				$property->setAccessible(true);
				$class->addProperty(
					ltrim($property->getName(), '_'),
					$this->makeScalarValueOnly($property->getValue($packageDescriptorEntity))
				)->setVisibility('protected');
			}
		}

		FileSystem::write($this->getPath(), '<?php' . "\n\n" . $class);
	}


	private function getPath(int $ttl = 3): string
	{
		static $cache;

		if ($cache === null) {
			$dir = $this->basePath . '/cache/baraja/packageDescriptor';
			$cache = $dir . '/PackageDescriptorEntity.php';

			try {
				FileSystem::createDir($dir, 0777);
				if (\is_file($cache) === false) {
					FileSystem::write($cache, '');
				}
			} catch (IOException $e) {
				if ($ttl > 0) {
					$this->tryFixTemp($dir);

					return $this->getPath($ttl - 1);
				}

				throw new \InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
			}
		}

		return $cache;
	}


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
		if (\is_array($data) === true) {
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

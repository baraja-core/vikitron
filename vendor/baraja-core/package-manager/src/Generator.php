<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;
use Baraja\PackageManager\Exception\PackageDescriptorException;
use Composer\Autoload\ClassLoader;
use Nette\Neon\Neon;

final class Generator
{

	/** @var string */
	private $projectRoot;

	/** @var string[] */
	private $customPackagesNamePatterns;

	/** @var Storage */
	private $storage;


	/**
	 * @param string $projectRoot
	 * @param string[] $customPackagesNamePatterns
	 * @param Storage $storage
	 */
	public function __construct(string $projectRoot, array $customPackagesNamePatterns, Storage $storage)
	{
		$this->projectRoot = $projectRoot;
		$this->customPackagesNamePatterns = $customPackagesNamePatterns;
		$this->storage = $storage;
	}


	/**
	 * @internal
	 * @return PackageDescriptorEntity
	 * @throws PackageDescriptorException
	 */
	public function run(): PackageDescriptorEntity
	{
		$packageDescriptor = new PackageDescriptorEntity($this->customPackagesNamePatterns);

		$composerJson = $this->storage->haystackToArray(
			json_decode((string) file_get_contents($this->projectRoot . '/composer.json'))
		);

		$packageDescriptor->setComposer($composerJson);
		$packageDescriptor->setPackages($packages = $this->getPackages($composerJson));

		$customRouters = [];
		$afterInstallScripts = [];

		foreach ($packages as $package) {
			if ($package['config'] !== null && isset($package['config']['routers'])) {
				foreach ($package['config']['routers']['data'] as $customRouter) {
					$customRouters[] = $customRouter;
				}
			}

			if ($package['config'] !== null && isset($package['config']['afterInstall'])) {
				foreach ($package['config']['afterInstall']['data'] as $afterInstallScript) {
					$afterInstallScripts[] = $afterInstallScript;
				}
			}
		}

		$packageDescriptor->setCustomRouters($customRouters);
		$packageDescriptor->setAfterInstallScripts($afterInstallScripts);

		return $packageDescriptor;
	}


	/**
	 * @param string[][] $composer
	 * @return mixed[]
	 * @throws PackageDescriptorException
	 */
	private function getPackages(array $composer): array
	{
		$packages = [];

		try {
			$packagesVersions = $this->getPackagesVersions();
		} catch (PackageDescriptorCompileException $e) {
			$packagesVersions = [];
		}

		$packageDirs = array_merge($composer['require'], $packagesVersions);

		// Find other packages
		foreach (new \DirectoryIterator($this->projectRoot . '/vendor') as $vendorNamespace) {
			if ($vendorNamespace->isDir() === true && ($namespace = $vendorNamespace->getFilename()) !== '.' && $namespace !== '..') {
				foreach (new \DirectoryIterator($this->projectRoot . '/vendor/' . $namespace) as $packageName) {
					if ($packageName->isDir() === true && ($name = $packageName->getFilename()) !== '.' && $name !== '..'
						&& isset($packageDirs[$package = $namespace . '/' . $name]) === false
					) {
						$packageDirs[$package] = '*';
					}
				}
			}
		}

		foreach ($packageDirs as $name => $dependency) {
			if (is_dir($path = $this->projectRoot . '/vendor/' . ($name = mb_strtolower($name, 'UTF-8'))) === false) {
				continue;
			}

			$configPath = null;
			if (\is_file($path . '/common.neon') === true) {
				$configPath = $path . '/common.neon';
			}

			if (\is_file($path . '/config.neon') === true) {
				if ($configPath !== null) {
					throw new \RuntimeException('Can not use multiple config files. Please merge "' . $configPath . '" and "config.neon" to "common.neon".');
				}
				trigger_error('File "config.neon" is deprecated for Nette 3.0, please use "common.neon" for path: "' . $path . '".');
				$configPath = $path . '/config.neon';
			}

			if (is_file($composerPath = $path . '/composer.json') && json_decode(file_get_contents($composerPath)) === null) {
				PackageDescriptorCompileException::composerJsonIsBroken($name);
			}

			$packages[] = [
				'name' => $name,
				'version' => $packagesVersions[$name] ?? null,
				'dependency' => $dependency,
				'config' => $configPath !== null ? $this->formatConfigSections($configPath) : null,
				'composer' => is_file($composerPath)
					? $this->storage->haystackToArray(json_decode(file_get_contents($composerPath)))
					: null,
			];
		}

		return $packages;
	}


	/**
	 * @param string $path
	 * @return string[]|string[][]|mixed[][]
	 */
	private function formatConfigSections(string $path): array
	{
		$return = [];

		foreach (\is_array($neon = Neon::decode(file_get_contents($path))) ? $neon : [] as $part => $haystack) {
			if ($part === 'services') {
				$servicesList = '';

				foreach ($haystack as $key => $serviceClass) {
					$servicesList .= (\is_int($key) ? '- ' : $key . ': ') . Neon::encode($serviceClass) . "\n";
				}

				$return[$part] = [
					'data' => $servicesList,
					'rewrite' => false,
				];
			} else {
				$return[$part] = [
					'data' => $haystack,
					'rewrite' => true,
				];
			}
		}

		return $return;
	}


	/**
	 * @return string[]
	 * @throws PackageDescriptorCompileException
	 */
	private function getPackagesVersions(): array
	{
		$return = [];

		$packages = [];
		if (class_exists(ClassLoader::class, false)) {
			try {
				$lockFile = \dirname((new \ReflectionClass(ClassLoader::class))->getFileName()) . '/../../composer.lock';
			} catch (\ReflectionException $e) {
				$lockFile = null;
			}

			if (is_file($lockFile) === false) {
				PackageDescriptorCompileException::canNotLoadComposerLock($lockFile);
			}

			$composer = @json_decode(file_get_contents($lockFile)); // @ may not exist or be valid
			$packages = (array) @$composer->packages;
			usort($packages, function ($a, $b) {
				return strcmp($a->name, $b->name);
			});
		}

		foreach ($packages as $package) {
			$return[$package->name] = $package->version
				. (strpos($package->version, 'dev') === false ? '' : ' #' . substr($package->source->reference, 0, 4));
		}

		return $return;
	}
}

<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;
use Baraja\PackageManager\Exception\PackageDescriptorException;
use Composer\Autoload\ClassLoader;
use Nette\Neon\Neon;
use Nette\Utils\Strings;

class Generator
{

	/**
	 * @var string
	 */
	private $projectRoot;

	/**
	 * @var string[]
	 */
	private $customPackagesNamePatterns;

	/**
	 * @var Storage
	 */
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
	public function generate(): PackageDescriptorEntity
	{
		$packageDescriptor = new PackageDescriptorEntity($this->customPackagesNamePatterns);

		$composerJson = $this->storage->haystackToArray(
			json_decode(
				(string) file_get_contents($this->projectRoot . '/composer.json')
			)
		);

		$packages = $this->getPackages($composerJson);
		$packageDescriptor->setComposer($composerJson);
		$packageDescriptor->setPackages($packages);

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
	 * @return string[][]
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

		$allPackages = array_merge(
			$composer['require'],
			$packagesVersions
		);

		foreach ($allPackages as $packageName => $dependency) {
			$packageName = Strings::lower($packageName);
			$path = $this->projectRoot . '/vendor/' . $packageName;

			if (!is_dir($path)) {
				continue;
			}

			$configPath = null;
			if (\is_file($path . '/common.neon')) {
				$configPath = $path . '/common.neon';
			} elseif (\is_file($path . '/config.neon')) {
				$configPath = $path . '/config.neon';
			}

			$composerPath = $path . '/composer.json';

			if (is_file($composerPath) && json_decode(file_get_contents($composerPath)) === null) {
				PackageDescriptorCompileException::composerJsonIsBroken($packageName);
			}

			$item = [
				'name' => $packageName,
				'version' => $packagesVersions[$packageName] ?? null,
				'dependency' => $dependency,
				'config' => $configPath !== null ? $this->formatConfigSections($configPath) : null,
				'composer' => is_file($composerPath)
					? $this->storage->haystackToArray(
						json_decode(
							file_get_contents($composerPath)
						)
					) : null,
			];

			$packages[] = $item;
		}

		return $packages;
	}

	/**
	 * @param string $path
	 * @return string[]
	 */
	private function formatConfigSections(string $path): array
	{
		$return = [];
		$neon = Neon::decode(file_get_contents($path));

		foreach (\is_array($neon) ? $neon : [] as $part => $haystack) {
			if ($part === 'services') {
				$servicesList = '';

				foreach ($haystack as $serviceKey => $serviceClass) {
					$servicesList .= (\is_int($serviceKey) ? '- ' : $serviceKey . ': ')
						. Neon::encode($serviceClass) . "\n";
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

			if (!is_file($lockFile)) {
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
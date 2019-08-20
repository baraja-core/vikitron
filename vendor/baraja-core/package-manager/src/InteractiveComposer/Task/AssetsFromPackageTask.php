<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;
use Baraja\PackageManager\Exception\TaskException;

class AssetsFromPackageTask extends BaseTask
{

	/**
	 * @return bool
	 * @throws TaskException
	 */
	public function run(): bool
	{
		$packageDescriptor = $this->packageRegistrator->getPackageDescriptorEntity();
		$basePath = \dirname(__DIR__, 5) . '/';

		try {
			if (\count($packageDescriptor->getPackagest()) === 0) {
				return false;
			}
		} catch (PackageDescriptorCompileException $e) {
			return false;
		}

		echo 'BasePath:    ' . $basePath . "\n";
		echo 'ProjectRoot: ' . \rtrim(\dirname($basePath), '/') . '/' . "\n\n";

		$namePatterns = $this->packageRegistrator->getPackageDescriptorEntity()->getCustomPackagesNamePatterns();

		foreach (glob($basePath . '*') ?? [] as $namespace) {
			if (\is_dir($namespace)) {
				$isCustom = false;
				foreach ($namePatterns as $pattern) {
					if (preg_match('/' . $pattern . '/', (string) preg_replace('/^.+\/([^\/]+)$/', '$1', $namespace))) {
						$isCustom = true;
						break;
					}
				}

				if (!$isCustom) {
					continue;
				}

				foreach (glob($namespace . '/*') ?? [] as $package) {
					if (\is_dir($package)) {
						$this->processPackage(rtrim($package) . '/', $basePath);
					}
				}
			}
		}

		return true;
	}

	public function getName(): string
	{
		return 'Assets from package copier';
	}

	/**
	 * @param string $path
	 * @param string $basePath
	 * @throws TaskException
	 */
	private function processPackage(string $path, string $basePath): void
	{
		$this->copyInstallDir($path . 'install/', \rtrim(\dirname($basePath), '/') . '/');
		$this->copyInstallDir($path . 'update/', \rtrim(\dirname($basePath), '/') . '/', true);
	}

	/**
	 * @param string $source
	 * @param string $projectRoot
	 * @param bool $forceUpdate
	 * @return bool
	 * @throws TaskException
	 */
	private function copyInstallDir(string $source, string $projectRoot, bool $forceUpdate = false): bool
	{
		if (!\is_dir($source)) {
			return false;
		}

		echo '|';

		clearstatcache();
		$this->copyFilesRecursively($source, '/', $projectRoot, $forceUpdate);
		clearstatcache();

		return true;
	}

	/**
	 * @param string $basePath
	 * @param string $path
	 * @param string $projectRoot
	 * @param bool $forceUpdate
	 * @throws TaskException
	 */
	private function copyFilesRecursively(string $basePath, string $path, string $projectRoot, bool $forceUpdate): void
	{
		foreach (scandir(rtrim(preg_replace('/\/+/', '/', $basePath . '/' . $path), '/'), 1) as $file) {
			if ($file !== '.' && $file !== '..') {
				$pathWithFile = preg_replace('/\/+/', '/', $path . '/' . $file);
				$projectFilePath = rtrim($projectRoot, '/') . '/' . ltrim($pathWithFile, '/');

				if (\is_dir($basePath . '/' . $pathWithFile)) {
					if (\is_dir($projectFilePath) || \mkdir($projectFilePath, 0777, true)) {
						echo '.';
					} else {
						TaskException::canNotCreateProjectDirectory($projectFilePath);
					}

					$this->copyFilesRecursively($basePath, $pathWithFile, $projectRoot, $forceUpdate);
				} elseif ($forceUpdate === false && \is_file($projectFilePath) === true) {
					echo '.';
				} else {
					$safeCopy = $this->safeCopy(
						$basePath . '/' . $pathWithFile,
						(string) preg_replace('/^(.*?)(\.dist)?$/', '$1', $projectFilePath)
					);

					if ($safeCopy === null || $safeCopy === true) {
						echo '.';
					} else {
						TaskException::canNotCopyFile($basePath . '/' . $pathWithFile);
					}
				}
			}
		}
	}

	/**
	 * Copy file with exactly content or throw exception.
	 * If case of error try repeat copy 3 times by $ttl.
	 *
	 * @param string $from
	 * @param string $to
	 * @param int $ttl
	 * @return bool|null
	 * @throws TaskException
	 */
	private function safeCopy(string $from, string $to, int $ttl = 3): ?bool
	{
		if (($fromHash = md5_file($from)) === (file_exists($to) ? md5_file($to) : null)) {
			return null;
		}

		if (($copy = copy($from, $to)) === false || md5_file($to) !== $fromHash) {
			if ($ttl > 0) {
				clearstatcache();

				return $this->safeCopy($from, $to, $ttl - 1);
			}

			TaskException::canNotCopyProjectFile($from, $to);
		}

		return $copy;
	}

}
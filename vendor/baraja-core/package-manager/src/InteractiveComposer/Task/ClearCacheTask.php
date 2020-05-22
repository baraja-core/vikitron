<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Helpers;
use Nette\Utils\FileSystem;

/**
 * Priority: 100
 */
final class ClearCacheTask extends BaseTask
{

	/** @var string[] */
	public $tempDirectories = ['proxies'];


	/**
	 * @return bool
	 */
	public function run(): bool
	{
		if (Helpers::functionIsAvailable('opcache_reset')) {
			opcache_reset();
		}

		$cachePath = ($tempPath = \dirname(__DIR__, 6) . '/temp') . '/cache';

		if (is_file($unlinkPath = $tempPath . '/_packageDescriptor/PackageDescriptorEntity.php') === true) {
			unlink($unlinkPath);
		}

		echo 'Path: ' . $cachePath;

		FileSystem::delete($cachePath);
		foreach ($this->tempDirectories ?? [] as $tempDirectory) {
			FileSystem::delete($tempPath . '/' . $tempDirectory);
		}

		return true;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'Clear cache';
	}
}

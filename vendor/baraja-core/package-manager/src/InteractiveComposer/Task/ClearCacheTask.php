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


	public function run(): bool
	{
		if (Helpers::functionIsAvailable('opcache_reset')) {
			@opcache_reset();
		}

		$tempPath = \dirname(__DIR__, 6) . '/temp';
		if (is_file($unlinkPath = $tempPath . '/_packageDescriptor/PackageDescriptorEntity.php') === true) {
			unlink($unlinkPath);
		}

		echo 'Path: ' . $tempPath;

		FileSystem::delete($tempPath);
		foreach ($this->tempDirectories ?? [] as $tempDirectory) {
			FileSystem::delete($tempPath . '/' . $tempDirectory);
		}

		return true;
	}


	public function getName(): string
	{
		return 'Clear cache';
	}
}

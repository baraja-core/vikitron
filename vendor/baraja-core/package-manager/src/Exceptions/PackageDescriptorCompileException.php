<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Exception;


class PackageDescriptorCompileException extends PackageDescriptorException
{

	/**
	 * @param string $lockPath
	 * @throws PackageDescriptorCompileException
	 */
	public static function canNotLoadComposerLock(string $lockPath): void
	{
		throw new self('Can not load [composer.lock]. Path: "' . $lockPath . '".');
	}

	/**
	 * @param string $packageName
	 * @throws PackageDescriptorCompileException
	 */
	public static function composerJsonIsBroken(string $packageName): void
	{
		throw new self('Composer.json of "' . $packageName . '" does not exist or is broken.');
	}

}
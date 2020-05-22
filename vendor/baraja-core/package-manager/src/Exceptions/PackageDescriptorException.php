<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Exception;


class PackageDescriptorException extends \Exception
{

	/**
	 * @param string $path
	 * @throws PackageDescriptorException
	 */
	public static function canNotCreateTempDir(string $path): void
	{
		throw new self('Can not create temp dir on path "' . $path . '"' . "\n" . error_get_last()['message']);
	}


	/**
	 * @param string $path
	 * @throws PackageDescriptorException
	 */
	public static function canNotCreateTempFile(string $path): void
	{
		throw new self('Can not create temp file on path "' . $path . '"' . "\n" . error_get_last()['message']);
	}


	/**
	 * @param string $path
	 * @throws PackageDescriptorException
	 */
	public static function tempFileGeneratingError(string $path): void
	{
		throw new self(
			'Can not regenerate PackageDescriptor temp file on path "' . $path . '"'
			. "\n" . error_get_last()['message']
		);
	}


	/**
	 * @param string $path
	 * @throws PackageDescriptorException
	 */
	public static function canNotRewritePackageNeon(string $path): void
	{
		throw new self(
			'Can not rewrite package.neon. Path: "' . $path . '"'
			. "\n" . error_get_last()['message']
		);
	}
}
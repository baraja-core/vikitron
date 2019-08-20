<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Exception;


class TaskException extends PackageDescriptorException
{

	/**
	 * @param string $path
	 * @throws TaskException
	 */
	public static function canNotCopyFile(string $path): void
	{
		throw new self('Can not copy "' . $path . '".');
	}

	/**
	 * @param string $path
	 * @throws TaskException
	 */
	public static function canNotCreateProjectDirectory(string $path): void
	{
		throw new self('Can not create directory "' . $path . '".');
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @throws TaskException
	 */
	public static function canNotCopyProjectFile(string $from, string $to): void
	{
		$errorMessage = null;
		static $pattern = '/\s*\[\<a[^>]+>[a-z0-9\.\-\_\(\)]+<\/a>\]\s*/i';

		if (($lastError = error_get_last()) && isset($lastError['message'])) {
			$errorMessage = trim((string) preg_replace($pattern, ' ', (string) $lastError['message']));
		}

		throw new self(
			'Can not copy file "' . $from . '" => "' . $to . '"'
			. ($errorMessage !== null ? ': ' . $errorMessage : '')
		);
	}

}
<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Error;
use function is_array;
use function is_callable;
use function is_object;

final class Helpers
{

	/** @throws Error */
	public function __construct()
	{
		throw new Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	public static function getBaseUrl(bool $useCache = true): ?string
	{
		static $return;

		if ($useCache === true && $return !== null) {
			return $return;
		}
		if (($currentUrl = self::getCurrentUrl()) !== null) {
			if (preg_match('/^(https?:\/\/.+)\/www\//', $currentUrl, $localUrlParser)) {
				$return = $localUrlParser[0];
			} elseif (preg_match('/^(https?:\/\/[^\/]+)/', $currentUrl, $publicUrlParser)) {
				$return = $publicUrlParser[1];
			}
		}
		if ($return !== null) {
			$return = rtrim($return, '/');
		}

		return $return;
	}


	/**
	 * Return current absolute URL.
	 * Return null, if current URL does not exist (for example in CLI mode).
	 */
	public static function getCurrentUrl(): ?string
	{
		if (!isset($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'])) {
			return null;
		}

		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
			. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}


	/**
	 * Convert dirty haystack to scalar haystack. If object implements __toString(), it will be called automatically.
	 *
	 * @param mixed $haystack
	 * @return mixed
	 */
	public static function strictScalarType($haystack, bool $rewriteObjectsToString = true)
	{
		if (is_array($haystack)) {
			$return = [];
			foreach ($haystack as $key => $value) {
				$return[$key] = self::strictScalarType($value, $rewriteObjectsToString);
			}

			return $return;
		}
		if (is_object($haystack) === true && is_callable($haystack) === false) {
			if ($rewriteObjectsToString === true && method_exists($haystack, '__toString')) {
				return (string) $haystack;
			}

			return get_class($haystack);
		}

		return $haystack;
	}
}

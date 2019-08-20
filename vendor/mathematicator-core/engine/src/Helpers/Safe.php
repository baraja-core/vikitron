<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Helper;


use Nette\StaticClass;

class Safe
{

	use StaticClass;

	/**
	 * Convert dirty haystack to scalar haystack. If object implements __toString(), it will be called automatically.
	 *
	 * @param mixed $haystack
	 * @param bool $rewriteObjectsToString
	 * @return mixed
	 */
	public static function strictScalarType($haystack, bool $rewriteObjectsToString = true)
	{
		if (\is_array($haystack)) {
			$return = [];

			foreach ($haystack as $key => $value) {
				$return[$key] = self::strictScalarType($value, $rewriteObjectsToString);
			}

			return $return;
		}

		if (\is_scalar($haystack)) {
			return $haystack;
		}

		if (\is_object($haystack)) {
			if ($rewriteObjectsToString === true && method_exists($haystack, '__toString')) {
				return (string) $haystack;
			}

			return get_class($haystack);
		}

		return $haystack;
	}


}
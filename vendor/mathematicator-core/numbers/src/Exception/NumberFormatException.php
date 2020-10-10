<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Exception;

/**
 * Exception thrown when invalid number format on input is provided.
 */
class NumberFormatException extends NumberException
{

	/**
	 * @param string $haystack
	 * @throws self
	 */
	public static function invalidInput(string $haystack): void
	{
		throw new self('Invalid input format, because haystack "' . $haystack . '" given.');
	}
}

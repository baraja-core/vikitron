<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Exception;

/**
 * Exception thrown when result cannot be provided because final number would be out of allowed set.
 */
class UnsupportedConversionException extends NumberException
{
	public function __construct(string $haystack = '')
	{
		parent::__construct('Conversion for "' . $haystack . '" is valid, but not supported yet.');
	}
}

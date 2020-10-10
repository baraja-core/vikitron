<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Exception;

/**
 * Exception thrown when result cannot be provided because final number would be out of allowed set.
 */
class OutOfSetException extends NumberException
{
	public function __construct(string $haystack = '', ?string $allowed = null)
	{
		$allowed = $allowed ? ' (allowed: ' . $allowed . ')' : '';

		parent::__construct('Input "' . $haystack . '" cannot be converted to the final set' . $allowed . ').');
	}
}

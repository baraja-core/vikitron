<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Exception;

/**
 * Exception thrown when a division by zero occurs.
 */
class DivisionByZeroException extends NumberException
{
	/**
	 * @param string $x
	 * @param string $y
	 * @throws self
	 */
	public static function canNotDivisionFractionByZero(string $x, string $y): void
	{
		throw new self('Can not division fraction [' . $x . ' / ' . $y . '] by zero.');
	}
}

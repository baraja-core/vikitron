<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Exception;


use Exception;

final class NumberException extends Exception
{

	/**
	 * @param string $haystack
	 * @throws NumberException
	 */
	public static function invalidInput(string $haystack): void
	{
		throw new self('Invalid input format, because haystack "' . $haystack . '" given.');
	}


	/**
	 * @param string $x
	 * @param string $y
	 * @throws NumberException
	 */
	public static function canNotDivisionFractionByZero(string $x, string $y): void
	{
		throw new self('Can not division fraction [' . $x . ' / ' . $y . '] by zero.');
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Validator;


use Mathematicator\Numbers\Converter\RomanToInt;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Nette\StaticClass;
use Nette\Utils\Strings;

/**
 * Checks whether the roman number is valid in modern set. (all positive integers and fractions /12)
 */
final class RomanNumberValidator
{
	use StaticClass;

	/**
	 * @param string $romanNumber
	 * @param bool $allowZero
	 * @return bool
	 */
	public static function validate(string $romanNumber, bool $allowZero = true): bool
	{
		if (strlen($romanNumber) === 0) {
			return false;
		}

		$normalizedInput = Strings::upper($romanNumber);

		if ($allowZero && $normalizedInput === 'N') {
			return true;
		}

		try {
			RomanToInt::convert($romanNumber);
		} catch (NumberFormatException $e) {
			return false;
		}
		return true;
	}


	/**
	 * @param string $romanNumber
	 * @param bool $allowZero
	 * @return bool
	 */
	public static function isOptimal(string $romanNumber, bool $allowZero = true): bool
	{
		if (strlen($romanNumber) === 0) {
			return false;
		}

		$normalizedInput = Strings::upper($romanNumber);

		if ($allowZero && $normalizedInput === 'N') {
			return true;
		}

		preg_match('/^_*/', $normalizedInput, $leadingUnderscoresMatches);

		$leadingUnderscoresCount = isset($leadingUnderscoresMatches[0]) ? strlen($leadingUnderscoresMatches[0]) : 0;

		$regex = '';
		for ($i = $leadingUnderscoresCount; $i >= 0; $i--) {
			$regex .= self::getRegex($i);
		}

		return (bool) preg_match('/^' . $regex . '$/', $normalizedInput);
	}


	/**
	 * @param int $underscoreCount
	 * @return string
	 */
	private static function getRegex($underscoreCount = 0): string
	{
		return '(_{' . $underscoreCount . '}M){0,3}((_{' . $underscoreCount . '}CM)|(_{' . $underscoreCount . '}CD)|(_{' . $underscoreCount . '}D)?(_{' . $underscoreCount . '}C){0,3})((_{' . $underscoreCount . '}XC)|(_{' . $underscoreCount . '}XL)|(_{' . $underscoreCount . '}L)?(_{' . $underscoreCount . '}X){0,3})((_{' . $underscoreCount . '}IX)|(_{' . $underscoreCount . '}IV)|(_{' . $underscoreCount . '}V)?(_{' . $underscoreCount . '}I){0,3})';
	}
}

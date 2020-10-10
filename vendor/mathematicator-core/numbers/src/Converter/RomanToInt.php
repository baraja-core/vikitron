<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Nette\StaticClass;
use Nette\Utils\Strings;
use Stringable;

/**
 * Convert roman numerals to integer
 *
 * @see https://en.wikipedia.org/wiki/Roman_numerals
 */
final class RomanToInt
{
	use StaticClass;

	/** @var int[] */
	private static $conversionTable = [
		'M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1,
	];

	/** @var int[][] */
	private static $conversionTableCache = [];


	/**
	 * @param string $romanNumberInput
	 * @return BigInteger
	 * @throws NumberFormatException
	 */
	public static function convert(string $romanNumberInput): BigInteger
	{
		$romanNumber = Strings::upper($romanNumberInput);
		$romanNumberSplit = (array) preg_split('/(?<=[IVXLCDM])(?=[_IVXLCDM])/', $romanNumber, -1, PREG_SPLIT_NO_EMPTY);
		$romanLength = count($romanNumberSplit);

		// Get count of leading underscores (e.g. 2 for __M)
		preg_match('/^_*/', $romanNumber, $leadingUnderscoresMatches);
		$leadingUnderscoresCount = isset($leadingUnderscoresMatches[0]) ? strlen($leadingUnderscoresMatches[0]) : 0;
		$conversionTable = self::getConversionTable($leadingUnderscoresCount);

		$return = 0;
		for ($i = 0; $i < $romanLength; $i++) {
			$actualChar = (string) $romanNumberSplit[$i];

			// Check whether is there a next roman numeral
			$nextChar = ($i + 1 < $romanLength) ? (string) $romanNumberSplit[$i + 1] : null;

			[$convertedIntValue, $nextIntValue] = self::convertCharPair($actualChar, $conversionTable, $nextChar);

			if ($nextIntValue !== null && $nextIntValue > $convertedIntValue) {
				$return += $nextIntValue - $convertedIntValue;
				$i++; // skip next number (already solved)
			} else {
				$return += $convertedIntValue;
			}
		}

		return BigInteger::of($return);
	}


	/**
	 * @param BigNumber|int|string|Stringable $input
	 * @return string
	 */
	public static function reverse($input): string
	{
		return IntToRoman::convert($input);
	}


	/**
	 * @param int $underscoresCount
	 * @return int[]
	 */
	public static function getConversionTable(int $underscoresCount = 0): array
	{
		if (isset(self::$conversionTableCache[(string) $underscoresCount])) {
			return self::$conversionTableCache[(string) $underscoresCount];
		}

		$outTable = self::$conversionTable;

		for ($thousandsIterator = 1; $thousandsIterator <= $underscoresCount; $thousandsIterator++) {
			$prependString = str_repeat('_', $thousandsIterator);
			foreach (self::$conversionTable as $tableLineKey => $tableLine) {
				if ($tableLineKey === 'I') {
					continue;
				}

				// Handle e.g. X => _X
				$prependedRomanNumeral = $prependString . substr((string) $tableLineKey, 0, 1);

				// Handle e.g. IX => _I_X
				if (substr((string) $tableLineKey, 1, 1)) {
					$prependedRomanNumeral .= $prependString . substr((string) $tableLineKey, 1, 1);
				}

				$outTable[$prependedRomanNumeral] = (int) $tableLine * (1000 ** $thousandsIterator);
			}
		}

		arsort($outTable);

		self::$conversionTableCache[(string) $underscoresCount] = $outTable;

		return $outTable;
	}


	/**
	 * Translates current and next character (if provided) from a given translation table and removes all higher
	 * values from the table to maintain Roman numeral validity.
	 *
	 * @param string $romanChar
	 * @param int[] $conversionTable
	 * @param string|null $nextRomanChar
	 * @return array<int, int|null>
	 */
	private static function convertCharPair(string $romanChar, array &$conversionTable, ?string $nextRomanChar = null): array
	{
		if (!isset($conversionTable[$romanChar]) || ($nextRomanChar !== null && !isset($conversionTable[$nextRomanChar]))) {
			NumberFormatException::invalidInput("$romanChar");
		}

		$out = [$conversionTable[$romanChar], $nextRomanChar ? $conversionTable[$nextRomanChar] : null];

		foreach ($conversionTable as $conversionTableKey => $conversionTableItem) {
			if ($conversionTableKey === $romanChar || $conversionTableKey === $romanChar . $nextRomanChar) {
				// E.g. if X || IX
				break;
			} else {
				// Removes all higher values from the table to maintain Roman numeral validity.
				unset($conversionTable[$conversionTableKey]);
			}
		}

		return $out;
	}
}

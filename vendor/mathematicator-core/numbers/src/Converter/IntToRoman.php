<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Mathematicator\Numbers\Exception\OutOfSetException;
use Stringable;

/**
 * Convert an integer to roman numerals
 *
 * Tip: Use validators if you want to do custom checks (e.g. not zero, or in original ancient set)
 *
 * @see https://en.wikipedia.org/wiki/Roman_numerals
 * @see https://www.wolframalpha.com/input/?i=1000000+to+roman
 * @see https://www.calculatorsoup.com/calculators/conversions/roman-numeral-converter.php
 */
final class IntToRoman extends IntToRomanBasic
{

	/**
	 * @param BigNumber|int|string|Stringable $input
	 * @return string
	 * @throws OutOfSetException
	 */
	public static function convert($input): string
	{
		$allowedSetDescription = 'integers >= 0';

		try {
			$int = BigInteger::of((string) $input);
		} catch (RoundingNecessaryException $e) {
			throw new OutOfSetException($input . ' (not integer)', $allowedSetDescription);
		}

		if ($int->isLessThan(0)) {
			throw new OutOfSetException($input . ' (negative)', $allowedSetDescription);
		}

		$out = '';

		// Prepare a conversion table
		$numberLength = strlen((string) $int);
		$numberThousands = ($numberLength - $numberLength % 3) / 3;
		$conversionTable = RomanToInt::getConversionTable($numberThousands);

		// Process each roman numeral
		foreach ($conversionTable as $roman => $value) {
			$matches = $int->dividedBy($value, RoundingMode::DOWN)->toInt();
			$out .= str_repeat($roman, $matches);
			$int = $int->mod($value);
		}

		return $out;
	}


	/**
	 * @param BigNumber|int|string|Stringable $input
	 * @return string
	 * @throws OutOfSetException
	 */
	public static function convertToLatex($input): string
	{
		$out = self::convert($input);

		// Get count of leading underscores (e.g. 2 for __M)
		preg_match('/^_*/', $out, $leadingUnderscoresMatches);
		$leadingUnderscoresCount = isset($leadingUnderscoresMatches[0]) ? strlen($leadingUnderscoresMatches[0]) : 0;

		// Convert underscores to latex overline
		for ($i = $leadingUnderscoresCount; $i > 0; $i--) {
			$out = (string) preg_replace('/_([IVXLCDM]|(\\\overline\{[\w{}]*\}))/', '\\overline{$1}', $out);
		}

		return $out;
	}
}

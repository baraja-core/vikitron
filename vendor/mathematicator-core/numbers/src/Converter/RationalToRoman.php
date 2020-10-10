<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\BigRational;
use Mathematicator\Numbers\Exception\OutOfSetException;
use Nette\StaticClass;
use Stringable;

/**
 * Convert rational number to basic roman fractions (original ancient set)
 *
 * @see https://en.wikipedia.org/wiki/Roman_numerals
 */
final class RationalToRoman
{
	use StaticClass;

	/** @var string[] */
	protected static $fractionConversionTable = [
		'1/12' => '·',
		'2/12' => '··',
		'3/12' => '···',
		'4/12' => '····',
		'5/12' => '·····',
		'6/12' => 'S',
		'7/12' => 'S·',
		'8/12' => 'S··',
		'9/12' => 'S···',
		'10/12' => 'S····',
		'11/12' => 'S·····',
	];


	/**
	 * @param BigNumber|int|string|Stringable $input
	 * @return string
	 * @throws OutOfSetException
	 * @see https://en.wikipedia.org/wiki/Roman_numerals (Fractions)
	 */
	public static function convert($input): string
	{
		$rationalNumber = BigRational::of((string) $input)->simplified();

		if ($rationalNumber->isLessThan('1/12')) {
			throw new OutOfSetException((string) $input . ' (less than 1/12)', 'integers >= 0, fractions /12');
		}

		$denominatorOriginal = $rationalNumber->getDenominator();

		if (in_array((string) $denominatorOriginal, ['1', '2', '3', '4', '6', '12'], true) && $denominatorOriginal->isLessThanOrEqualTo(12)) {
			$toFinalMultiplier = BigInteger::of(12)->dividedBy($denominatorOriginal)->toInt();
			$numeratorMultiplied = $rationalNumber->getNumerator()->multipliedBy($toFinalMultiplier);

			$intFinal = $numeratorMultiplied->quotient(12);
			$numeratorFinal = $numeratorMultiplied->mod(12)->toInt();

			$out = '';

			// Integer part
			if ($intFinal->isGreaterThan(0)) {
				$out .= IntToRoman::convert($intFinal);
			}


			// Fraction part
			if ($numeratorFinal > 0) {
				$out .= self::$fractionConversionTable["$numeratorFinal/12"];
			}

			return $out;
		} else {
			throw new OutOfSetException((string) $input);
		}
	}
}

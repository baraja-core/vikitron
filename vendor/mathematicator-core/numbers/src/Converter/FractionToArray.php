<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Mathematicator\Numbers\Entity\Fraction;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Nette\StaticClass;

final class FractionToArray
{
	use StaticClass;

	/**
	 * @param Fraction $fraction
	 * @param bool $simplify
	 * @return mixed[]
	 * @throws NumberFormatException
	 */
	public static function convert(Fraction $fraction, bool $simplify = true): array
	{
		if (!$fraction->isValid()) {
			throw new NumberFormatException('Fraction is not valid!');
		}

		$numeratorOut = self::convertPart($fraction->getNumerator(), false, $simplify);
		$denominatorOut = self::convertPart($fraction->getDenominator(), true, $simplify);

		return [$numeratorOut, $denominatorOut];
	}


	/**
	 * @param mixed[] $fraction
	 * @return Fraction
	 * @throws NumberFormatException
	 */
	public static function reverse(array $fraction): Fraction
	{
		return ArrayToFraction::convert($fraction);
	}


	/**
	 * @param Fraction|string|null $part
	 * @param bool $isDenominator
	 * @param bool $simplify
	 * @return string|mixed[]|null
	 * @throws NumberFormatException
	 */
	private static function convertPart($part, bool $isDenominator, bool $simplify)
	{
		if ($isDenominator && $part === null) {
			if (!$simplify) {
				return '1';
			}
			return null;
		} elseif ($part instanceof Fraction) {
			return self::convert($part, $simplify);
		} else {
			return (string) $part;
		}
	}
}

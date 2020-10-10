<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Mathematicator\Numbers\Entity\Fraction;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Mathematicator\Numbers\HumanString\MathHumanStringBuilder;
use Mathematicator\Numbers\HumanString\MathHumanStringToolkit;
use Nette\StaticClass;

final class FractionToHumanString
{
	use StaticClass;

	/**
	 * @param Fraction $fraction
	 * @param bool $simplify Remove denominator if === 1
	 * @return MathHumanStringBuilder
	 * @throws NumberFormatException
	 */
	public static function convert(Fraction $fraction, bool $simplify = true): MathHumanStringBuilder
	{
		if (!$fraction->isValid()) {
			throw new NumberFormatException('Fraction is not valid!');
		}

		$numeratorString = self::convertPart($fraction->getNumerator(), false, $simplify);
		$denominatorString = self::convertPart($fraction->getDenominator(), true, $simplify);

		return MathHumanStringToolkit::frac($numeratorString, $denominatorString);
	}


	/**
	 * @param Fraction|string|null $part
	 * @param bool $isDenominator
	 * @param bool $simplify Remove denominator if === 1
	 * @return string
	 */
	private static function convertPart($part, bool $isDenominator, bool $simplify)
	{
		if ($isDenominator && $part === null) {
			if (!$simplify) {
				return '1';
			}
			return '';
		} elseif ($part instanceof Fraction) {
			return (string) MathHumanStringToolkit::wrap(self::convert($part, $simplify), '(', ')');
		} else {
			return (string) $part;
		}
	}
}

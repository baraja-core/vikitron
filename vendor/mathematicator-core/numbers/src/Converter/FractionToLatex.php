<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Mathematicator\Numbers\Entity\Fraction;
use Mathematicator\Numbers\Latex\MathLatexBuilder;
use Mathematicator\Numbers\Latex\MathLatexToolkit;

final class FractionToLatex
{
	public static function convert(Fraction $fraction): MathLatexBuilder
	{
		$numerator = $fraction->getNumerator();
		$numeratorString = '';

		if ($numerator instanceof Fraction) {
			$numeratorString .= self::convert($numerator);
		} else {
			$numeratorString .= $numerator;
		}

		$denominator = $fraction->getDenominatorNotNull();
		$denominatorString = '';

		if ($denominator instanceof Fraction) {
			$denominatorString .= self::convert($denominator);
		} else {
			$denominatorString .= $denominator;
		}

		return MathLatexToolkit::frac($numeratorString, $denominatorString);
	}
}

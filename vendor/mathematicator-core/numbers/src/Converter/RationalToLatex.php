<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Brick\Math\BigRational;
use Mathematicator\Numbers\Latex\MathLatexBuilder;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Nette\StaticClass;

final class RationalToLatex
{
	use StaticClass;

	public static function convert(BigRational $rationalNumber): MathLatexBuilder
	{
		return MathLatexToolkit::frac(
			(string) $rationalNumber->getNumerator(),
			(string) $rationalNumber->getDenominator()
		);
	}
}

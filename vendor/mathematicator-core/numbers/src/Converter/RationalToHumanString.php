<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Converter;


use Brick\Math\BigRational;
use Mathematicator\Numbers\HumanString\MathHumanStringBuilder;
use Mathematicator\Numbers\HumanString\MathHumanStringToolkit;
use Nette\StaticClass;

final class RationalToHumanString
{
	use StaticClass;

	/**
	 * @param BigRational $rationalNumber
	 * @return MathHumanStringBuilder
	 */
	public static function convert(BigRational $rationalNumber): MathHumanStringBuilder
	{
		return MathHumanStringToolkit::frac(
			(string) $rationalNumber->getNumerator(),
			(string) $rationalNumber->getDenominator()
		);
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Helpers;


use Brick\Math\BigNumber;
use Mathematicator\Engine\Exception\MathematicatorException;
use Mathematicator\Numbers\Entity\Fraction;
use Mathematicator\Numbers\Latex\MathLatexToolkit;

/**
 * @internal
 */
final class FractionHelper
{

	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * @throws MathematicatorException
	 */
	public static function stringToSimpleFraction(string $input): Fraction
	{
		$fraction = new Fraction();

		switch (\count($explode = explode('/', $input))) {
			case 1:
				$fraction->setNumerator($input);
				$fraction->setDenominator(1);

				return $fraction;
			case 2:
				$fraction->setNumerator($explode[0]);
				$fraction->setDenominator($explode[1]);

				return $fraction;
			default:
				throw new MathematicatorException('Parsing of compound fractions is not supported.');
		}
	}


	/**
	 * @param bool $simplify Remove denominator if it is unnecessary
	 * @return string
	 */
	public static function fractionToLatex(Fraction $fraction, $simplify = false): string
	{
		$numerator = $fraction->getNumerator();
		$denominator = $fraction->getDenominatorNotNull();

		// Simplify (remove denominator) if it's wanted and possible
		if ($simplify && BigNumber::of((string) $denominator)->isEqualTo(1)) {
			return (string) $numerator;
		}
		if ($numerator instanceof Fraction) { // Compose LaTeX
			$numeratorLatex = self::fractionToLatex($numerator, $simplify);
		} else {
			$numeratorLatex = (string) $numerator;
		}
		if ($denominator instanceof Fraction) {
			$denominatorLatex = self::fractionToLatex($denominator, $simplify);
		} else {
			$denominatorLatex = $denominator;
		}

		// Create LaTeX
		return (string) MathLatexToolkit::frac($numeratorLatex, $denominatorLatex);
	}
}

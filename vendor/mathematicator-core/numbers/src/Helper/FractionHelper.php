<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Helper;


use Brick\Math\BigDecimal;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Mathematicator\Numbers\Converter\DecimalToFraction;
use Mathematicator\Numbers\Entity\FractionNumbersOnly;
use Mathematicator\Numbers\Exception\DivisionByZeroException;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Mathematicator\Numbers\PrimaryNumber;
use Nette\StaticClass;
use Nette\Utils\Validators;
use RuntimeException;

class FractionHelper
{
	use StaticClass;

	/**
	 * Automatically converts a fraction to a shortened form.
	 * A prime division is used to shorten the fractions. It is the fastest method for calculation.
	 *
	 * @param FractionNumbersOnly $fraction
	 * @param int $level
	 * @param int $maxLevel
	 * @return FractionNumbersOnly
	 * @throws NumberFormatException
	 */
	public static function toShortenForm(FractionNumbersOnly $fraction, int $level = 0, int $maxLevel = 100): FractionNumbersOnly
	{
		$numerator = $fraction->getNumerator();
		if ($numerator instanceof FractionNumbersOnly) {
			$numeratorValue = self::evaluate($numerator);
		} elseif ($numerator !== null) {
			$numeratorValue = BigDecimal::of($numerator);
		} else {
			throw new RuntimeException('Numerator have to be defined.');
		}

		$denominator = $fraction->getDenominatorNotNull();
		if ($denominator instanceof FractionNumbersOnly) {
			$denominatorValue = self::evaluate($denominator);
		} else {
			$denominatorValue = $denominator;
		}

		if ($denominatorValue->isEqualTo(0)) {
			DivisionByZeroException::canNotDivisionFractionByZero((string) $numeratorValue, (string) $denominatorValue);
		}

		$fractionValue = self::evaluate($fraction);

		if (!Validators::isNumericInt((string) $numerator) || !Validators::isNumericInt((string) $denominator)) {
			return DecimalToFraction::convert($fractionValue);
		}

		$numeratorValueAbs = $numeratorValue->abs();
		$denominatorValueAbs = $denominatorValue->abs();

		if ($level > $maxLevel) {
			return new FractionNumbersOnly($numeratorValue, $denominatorValue);
		}

		try {
			return new FractionNumbersOnly($fractionValue->toScale(0));
		} catch (RoundingNecessaryException $e) {
			// Fraction cannot be evaluated to simple number directly. So go on…
		}

		foreach (PrimaryNumber::getList() as $primaryNumber) {
			$primaryNumber = BigDecimal::of($primaryNumber);

			if (
				$primaryNumber->isGreaterThan($numeratorValueAbs)
				|| $primaryNumber->isGreaterThan($denominatorValueAbs)
			) {
				break;
			}

			if (
				NumberHelper::isModuloZero($numeratorValueAbs, $primaryNumber)
				&& NumberHelper::isModuloZero($denominatorValueAbs, $primaryNumber)
			) {
				return self::toShortenForm(
					new FractionNumbersOnly(
						$numeratorValue->dividedBy($primaryNumber),
						$denominatorValue->dividedBy($primaryNumber)
					),
					$level + 1,
					$maxLevel
				);
			}
		}

		return new FractionNumbersOnly($numeratorValue, $denominatorValue);
	}


	/**
	 * @param FractionNumbersOnly $fraction
	 * @param int|null $scale
	 * @param int $roundingMode
	 * @return BigDecimal
	 * @throws NumberFormatException
	 * @todo Change scale to arbitrary using math expression
	 */
	public static function evaluate(FractionNumbersOnly $fraction, ?int $scale = 100, int $roundingMode = RoundingMode::FLOOR): BigDecimal
	{
		if (!$fraction->isValid()) {
			throw new NumberFormatException('Cannot enumerate fraction without numerator.');
		}

		/** @var FractionNumbersOnly|BigDecimal $numerator */
		$numerator = $fraction->getNumerator();

		if ($numerator instanceof FractionNumbersOnly) {
			$numeratorEval = self::evaluate($numerator, $scale, $roundingMode);
		} else {
			$numeratorEval = $numerator;
		}

		$denominator = $fraction->getDenominatorNotNull();

		if ($denominator instanceof FractionNumbersOnly) {
			$denominatorEval = self::evaluate($denominator, $scale, $roundingMode);
		} else {
			$denominatorEval = $denominator;
		}

		$number = $numeratorEval->dividedBy($denominatorEval, $scale, $roundingMode);

		return $number;
	}
}

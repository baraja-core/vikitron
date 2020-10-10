<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Entity;


use ArrayAccess;
use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Stringable;

/**
 * Entity to store simple and compound fractions
 * that consists only from numbers (no functions, variables etc.)
 *
 * @implements ArrayAccess<int, mixed[]|string|null>
 */
final class FractionNumbersOnly extends Fraction implements ArrayAccess
{

	/** @var FractionNumbersOnly|BigDecimal|null */
	protected $numerator;

	/** @var FractionNumbersOnly|BigDecimal|null */
	protected $denominator;


	/**
	 * @param int|string|Stringable|BigNumber|FractionNumbersOnly|null $numerator optional
	 * @param int|string|Stringable|BigNumber|FractionNumbersOnly|null $denominator optional
	 */
	public function __construct($numerator = null, $denominator = null)
	{
		parent::__construct($numerator, $denominator);
	}


	/**
	 * @return FractionNumbersOnly|BigDecimal|null
	 */
	public function getNumerator()
	{
		return $this->numerator;
	}


	/**
	 * @param int|string|Stringable|BigNumber|FractionNumbersOnly|null $numerator
	 * @return self
	 * @throws NumberFormatException
	 */
	public function setNumerator($numerator)
	{
		if ($numerator instanceof self) {
			$numerator->setParentInNumerator($this);
			$this->numerator = $numerator;
		} elseif ($numerator instanceof Fraction) {
			throw new NumberFormatException(sprintf('You can set only %s for %s compound numerator.', self::class, self::class));
		} else {
			$this->numerator = BigDecimal::of((string) $numerator);
		}

		return $this;
	}


	/**
	 * @return FractionNumbersOnly|BigDecimal|null
	 */
	public function getDenominator()
	{
		return $this->denominator;
	}


	/**
	 * @param int|string|Stringable|BigNumber|Fraction $denominator
	 * @return FractionNumbersOnly
	 * @throws NumberFormatException
	 */
	public function setDenominator($denominator): self
	{
		if ($denominator instanceof self) {
			$denominator->setParentInDenominator($this);
			$this->denominator = $denominator;
		} elseif ($denominator instanceof Fraction) {
			throw new NumberFormatException(sprintf('You can set only %s for %s compound denominator.', self::class, self::class));
		} else {
			$this->denominator = BigDecimal::of((string) $denominator);
		}

		return $this;
	}


	/**
	 * @return FractionNumbersOnly|BigDecimal
	 */
	public function getDenominatorNotNull()
	{
		$denominator = $this->getDenominator();
		return ($denominator !== null) ? $denominator : BigDecimal::of('1');
	}
}

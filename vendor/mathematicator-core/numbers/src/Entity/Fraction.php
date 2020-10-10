<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Entity;


use ArrayAccess;
use Brick\Math\BigNumber;
use Mathematicator\Numbers\Converter\FractionToHumanString;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Stringable;

/**
 * Entity to store simple and compound fractions
 *
 * @implements ArrayAccess<int, mixed[]|string|null>
 */
class Fraction implements ArrayAccess
{
	use FractionArrayAccessTrait;

	/** @var Fraction|string|null */
	protected $numerator;

	/** @var Fraction|string|null */
	protected $denominator;

	/**
	 * Superior fraction (if in compound structure)
	 *
	 * @var Fraction|null
	 */
	protected $parentInNumerator;

	/**
	 * Superior fraction (if in compound structure)
	 *
	 * @var Fraction|null
	 */
	protected $parentInDenominator;


	/**
	 * @param int|string|Stringable|BigNumber|Fraction|null $numerator optional
	 * @param int|string|Stringable|BigNumber|Fraction|null $denominator optional If numerator is set, than 1 is default
	 */
	public function __construct($numerator = null, $denominator = null)
	{
		if ($numerator !== null) {
			$this->setNumerator($numerator);
		}
		if ($denominator !== null || $numerator !== null) {
			$this->setDenominator(($denominator !== null) ? $denominator : 1);
		}
	}


	public function __clone()
	{
		if (is_object($this->numerator)) {
			$this->numerator = clone $this->numerator;
		}
		if (is_object($this->denominator)) {
			$this->denominator = clone $this->denominator;
		}
		if (is_object($this->parentInNumerator)) {
			$this->parentInNumerator = clone $this->parentInNumerator;
		}
		if (is_object($this->parentInDenominator)) {
			$this->parentInDenominator = clone $this->parentInDenominator;
		}
	}


	/**
	 * Returns a human string (e.g. (5/2)/1).
	 *
	 * @return string
	 * @throws NumberFormatException
	 */
	public function __toString(): string
	{
		return (string) FractionToHumanString::convert($this);
	}


	/**
	 * Checks whether the fraction is valid for further computing.
	 *
	 * @return bool
	 */
	public function isValid(): bool
	{
		return $this->numerator !== null;
	}


	/**
	 * @return Fraction|string|null
	 */
	public function getNumerator()
	{
		return $this->numerator;
	}


	/**
	 * @param int|string|Stringable|BigNumber|Fraction|null $numerator
	 * @return self
	 */
	public function setNumerator($numerator)
	{
		if ($numerator instanceof self) {
			$numerator->setParentInNumerator($this);
			$this->numerator = $numerator;
		} elseif ($numerator === null) {
			$this->numerator = null;
		} else {
			$this->numerator = (string) $numerator;
		}

		return $this;
	}


	/**
	 * @return Fraction|string|null
	 */
	public function getDenominator()
	{
		return $this->denominator;
	}


	/**
	 * @param int|string|Stringable|BigNumber|Fraction|null $denominator
	 * @return self
	 */
	public function setDenominator($denominator)
	{
		if ($denominator instanceof self) {
			$denominator->setParentInDenominator($this);
			$this->denominator = $denominator;
		} elseif ($denominator === null) {
			$this->denominator = null;
		} else {
			$this->denominator = (string) $denominator;
		}

		return $this;
	}


	/**
	 * @return Fraction|string
	 */
	public function getDenominatorNotNull()
	{
		return $this->getDenominator() ?: '1';
	}


	public function getParent(): ?self
	{
		return $this->parentInNumerator ?: $this->parentInDenominator;
	}


	/**
	 * @param Fraction|null $parentInNumerator
	 * @return Fraction
	 */
	public function setParentInNumerator(?self $parentInNumerator): self
	{
		$this->parentInDenominator = null;
		$this->parentInNumerator = $parentInNumerator;

		return $this;
	}


	/**
	 * @param Fraction|null $parentInDenominator
	 * @return Fraction
	 */
	public function setParentInDenominator(?self $parentInDenominator): self
	{
		$this->parentInNumerator = null;
		$this->parentInDenominator = $parentInDenominator;

		return $this;
	}
}

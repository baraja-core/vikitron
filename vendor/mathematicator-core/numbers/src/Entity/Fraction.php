<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Entity;


use Brick\Math\BigNumber;
use Stringable;

/**
 * Entity to store simple and compound fractions
 */
class Fraction
{

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
	 * @param int|string|Stringable|BigNumber|Fraction|null $denominator optional
	 */
	public function __construct($numerator = null, $denominator = null)
	{
		if ($numerator) {
			$this->setNumerator($numerator);
		}
		if ($denominator) {
			$this->setDenominator($denominator);
		}
	}


	public function __toString(): string
	{
		return $this->getNumerator() . '/' . $this->getDenominator();
	}


	/**
	 * @return Fraction|string|null
	 */
	public function getNumerator()
	{
		return $this->numerator;
	}


	/**
	 * @param int|string|Stringable|BigNumber|Fraction $numerator
	 * @return Fraction
	 */
	public function setNumerator($numerator): self
	{
		if ($numerator instanceof self) {
			$numerator->setParentInNumerator($this);
			$this->numerator = $numerator;
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
	 * @param int|string|Stringable|BigNumber|Fraction $denominator
	 * @return Fraction
	 */
	public function setDenominator($denominator): self
	{
		if ($denominator instanceof self) {
			$denominator->setParentInDenominator($this);
			$this->denominator = $denominator;
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

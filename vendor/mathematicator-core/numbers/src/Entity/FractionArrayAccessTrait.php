<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Entity;


use Brick\Math\BigNumber;
use Mathematicator\Numbers\Converter\FractionToArray;
use Mathematicator\Numbers\Exception\NumberFormatException;
use RuntimeException;
use Stringable;

trait FractionArrayAccessTrait
{

	/**
	 * @param mixed $offset
	 * @param int|string|Stringable|BigNumber|Fraction|null $value
	 */
	public function offsetSet($offset, $value): void
	{
		if (in_array($offset, [0, 'numerator'], true)) {
			$this->setNumerator($value);
		} elseif (in_array($offset, [1, 'denominator'], true)) {
			$this->setDenominator($value);
		} else {
			throw new RuntimeException("Offset $offset could not exist for fractions.");
		}
	}


	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return in_array($offset, [0, 1, 'numerator', 'denominator'], true);
	}


	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset): void
	{
		if (in_array($offset, [0, 'numerator'], true)) {
			$this->setNumerator(null);
		} elseif (in_array($offset, [1, 'denominator'], true)) {
			$this->setDenominator(null);
		}
	}


	/**
	 * @param mixed $offset
	 * @return mixed[]|string|null Returns recursively [string, string] or string value
	 * @throws NumberFormatException
	 */
	public function offsetGet($offset)
	{
		return FractionToArray::convert($this)[($offset === 'numerator' ? 0 : ($offset === 'denominator' ? 1 : $offset))];
	}
}

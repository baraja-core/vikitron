<?php

declare(strict_types=1);

namespace Mathematicator\Numbers;


use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\BigRational;
use Mathematicator\Numbers\Entity\Number;
use Mathematicator\Numbers\Helper\NumberHelper;

/**
 * SmartNumber is an easy-to-use entity for interpreting numbers with some softly invalid inputs corrections
 * and comparing features.
 * Instance of SmartNumber is immutable (readonly since initialized). If you want to modify it,
 * create a new one by new SmartNumber(...)
 *
 * The class can store the following data types:
 * - Integer
 * - Decimal number
 * - Rational number
 *
 * Some methods decorates implementation from brick/math package.
 *
 * @property-read int|float|string|BigNumber $input
 * @property-read BigNumber $number
 */
final class SmartNumber extends Number
{

	/**
	 * @param int|float|string|BigNumber|Number $number
	 * @return self
	 */
	public static function of($number): self
	{
		return new self($number);
	}


	/**
	 * Checks if this number is strictly positive.
	 *
	 * @return bool
	 */
	public function isPositive(): bool
	{
		return $this->_number->isPositive();
	}


	/**
	 * Checks if this number is strictly negative.
	 *
	 * @return bool
	 */
	public function isNegative(): bool
	{
		return $this->_number->isNegative();
	}


	/**
	 * Checks if this number equals zero.
	 *
	 * @return bool
	 */
	public function isZero(): bool
	{
		return $this->_number->isZero();
	}


	/**
	 * Checks if this number is equal to the given one.
	 *
	 * @param BigNumber|int|float|string $that
	 *
	 * @return bool
	 */
	public function isEqualTo($that): bool
	{
		return $this->_number->isEqualTo($that);
	}


	/**
	 * Checks if this number is strictly lower than the given one.
	 *
	 * @param BigNumber|int|float|string $that
	 *
	 * @return bool
	 */
	public function isLessThan($that): bool
	{
		return $this->_number->isLessThan($that);
	}


	/**
	 * Checks if this number is lower than or equal to the given one.
	 *
	 * @param BigNumber|int|float|string $that
	 *
	 * @return bool
	 */
	public function isLessThanOrEqualTo($that): bool
	{
		return $this->_number->isLessThanOrEqualTo($that);
	}


	/**
	 * Checks if this number is strictly greater than the given one.
	 *
	 * @param BigNumber|int|float|string $that
	 *
	 * @return bool
	 */
	public function isGreaterThan($that): bool
	{
		return $this->_number->isGreaterThan($that);
	}


	/**
	 * Checks if this number is greater than or equal to the given one.
	 *
	 * @param BigNumber|int|float|string $that
	 *
	 * @return bool
	 */
	public function isGreaterThanOrEqualTo($that): bool
	{
		return $this->_number->isGreaterThanOrEqualTo($that);
	}


	/**
	 * Converts any user input to the internal state of the object.
	 * The parsing of numbers takes place in a safe way, in which the values are not distorted due to rounding.
	 * Numbers are handled like a string.
	 *
	 * @param int|float|string|BigNumber $input
	 * @throws Exception\NumberFormatException
	 */
	protected function setValue($input): void
	{
		try {
			parent::setValue($input);
			return;
		} catch (Exception\NumberFormatException $e) {
		} catch (Exception\DivisionByZeroException $e) {
			throw new Exception\DivisionByZeroException($e->getMessage());
		}

		// Handle some other softly invalid cases
		$input = NumberHelper::preprocessInput((string) $input, ['.'], ['', ' ']);

		try {
			parent::setValue($input);
			return;
		} catch (Exception\NumberFormatException $e) {
		}

		// Solve multiple positivity signs (e.g. --6 => 6, ---5 => -5, --5.2 => 5.2, --5/2 => 5/2)
		if (preg_match('/^([+-]{2,})(.*)$/', $input, $parseResult)) {
			$this->setValue((substr_count($parseResult[1], '-') % 2 === 0 ? '' : '-') . $parseResult[2]);
			return;
		}

		// Solve fraction with decimals
		if (preg_match('/^(\d*\.\d*)\/(\d*\.\d*)$/', $input, $parseResult)) {
			$numerator = BigDecimal::of($parseResult[1]);
			$denominator = BigDecimal::of($parseResult[2]);

			$multiplier = 10 * (($numerator->getScale() > $denominator->getScale()) ? $numerator->getScale() : $denominator->getScale());

			$this->_number = BigRational::nd($parseResult[1] * $multiplier, $parseResult[2] * $multiplier);
			return;
		}

		Exception\NumberFormatException::invalidInput($input);
	}
}

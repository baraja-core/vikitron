<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Entity;


use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\BigRational;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Mathematicator\Numbers\Converter\RationalToHumanString;
use Mathematicator\Numbers\Converter\RationalToLatex;
use Mathematicator\Numbers\Helper\NumberHelper;
use Mathematicator\Numbers\HumanString\MathHumanStringBuilder;
use Mathematicator\Numbers\HumanString\MathHumanStringToolkit;
use Mathematicator\Numbers\Latex\MathLatexBuilder;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Nette\SmartObject;

/**
 * Number is an entity for interpreting numbers with an arbitrary precision.
 * Instance of Number is immutable (readonly since initialized). If you want to modify it,
 * create a new one by new Number(...)
 *
 * The class can store the following data types:
 * - Integer
 * - Decimal number
 * - Rational number
 *
 * @property-read int|float|string|BigNumber $input
 * @property-read BigNumber $number
 */
class Number
{
	use SmartObject;

	/**
	 * Number main storage
	 *
	 * @var BigNumber
	 */
	protected $_number;

	/**
	 * Original user input
	 *
	 * @var int|float|string|BigNumber
	 */
	private $input;

	/** @var mixed[] */
	private $cache = [];


	/**
	 * @param int|float|string|BigNumber|self $number
	 * Allowed formats are: 123456789, 12345.6789, 5/8
	 * If you have a real user input in nonstandard format, please NumberHelper::preprocessInput method first
	 * @throws \Mathematicator\Numbers\Exception\NumberFormatException
	 */
	public function __construct($number)
	{
		$this->invalidateCache(); // Defines array cache indexes

		if ($number instanceof self) {
			$this->input = $number->getInput();
			$this->_number = $number->getNumber();
		} else {
			$this->input = $number;
			$this->setValue($number);
		}
	}


	/**
	 * @param int|float|string|BigNumber|Number $number
	 * @return self
	 */
	public static function of($number)
	{
		return new self($number);
	}


	/**
	 * Returns number in same type as stored
	 *
	 * @return BigNumber
	 */
	public function getNumber(): BigNumber
	{
		return $this->_number;
	}


	/**
	 * User real input
	 *
	 * @return int|float|string|BigNumber
	 */
	public function getInput()
	{
		return $this->input;
	}


	/**
	 * @param int $roundingMode
	 * @return int
	 */
	public function toInt(int $roundingMode = RoundingMode::FLOOR): int
	{
		return $this->toBigInteger($roundingMode)->toInt();
	}


	/**
	 * WARNING! Float is only an approximation. Float data type is not precise!
	 * Always use getDecimal() method for precise computing.
	 *
	 * @param int $rationalScaleLimit Limit scale if rounding is needed (rational numbers). Default: 10
	 * @param int $rationalRoundingMode Rounding mode for rational numbers
	 * @return float
	 */
	public function toFloat(int $rationalScaleLimit = 10, int $rationalRoundingMode = RoundingMode::FLOOR): float
	{
		$cacheKey = $rationalScaleLimit . '_' . $rationalRoundingMode;
		if (isset($this->cache['float'][$cacheKey])) {
			return $this->cache['float'][$cacheKey];
		} else {
			return $this->cache['float'][$cacheKey] = $this->toBigDecimal($rationalScaleLimit, $rationalRoundingMode)->toFloat();
		}
	}


	/**
	 * @param int $roundingMode
	 * @return BigInteger
	 * @throws RoundingNecessaryException
	 */
	public function toBigInteger(int $roundingMode = RoundingMode::FLOOR): BigInteger
	{
		return $this->_number->toScale(0, $roundingMode)->toBigInteger();
	}


	/**
	 * @param int $rationalScaleLimit Limit scale if rounding is needed (rational numbers). Default: 10
	 * @param int $rationalRoundingMode Rounding mode for rational numbers
	 * @return BigDecimal
	 */
	public function toBigDecimal(int $rationalScaleLimit = 10, int $rationalRoundingMode = RoundingMode::FLOOR): BigDecimal
	{
		$cacheKey = $rationalScaleLimit . '_' . $rationalRoundingMode;
		if (isset($this->cache['decimal'][$cacheKey])) {
			return $this->cache['decimal'][$cacheKey];
		} else {
			if ($this->_number instanceof BigRational) {
				$result = (string) $this->_number->getNumerator()->toBigDecimal()
					->dividedBy($this->_number->getDenominator(), $rationalScaleLimit, $rationalRoundingMode);

				return BigDecimal::of(NumberHelper::removeTrailingZeros($result));
			}

			return $this->cache['decimal'][$cacheKey] = $this->_number->toBigDecimal();
		}
	}


	/**
	 * Returns simple rational number (similar to getFraction() but
	 * without ArrayAccess and advance features).
	 * TIP: Use toBigRational(false) for faster first result (returns not simplified rational number)
	 *
	 * @param bool|null $simplify Simplify rational number output (null means to not simplify rational input, else simplify)
	 * @return BigRational
	 */
	public function toBigRational(?bool $simplify = null): BigRational
	{
		$simplify = ($simplify === true || ($simplify === null && !($this->_number instanceof BigRational)));

		if ($simplify) {
			return $this->toBigRationalSimplified();
		}

		if ($this->cache['rational']) {
			return $this->cache['rational'];
		} elseif ($this->_number instanceof BigRational) {
			return $this->cache['rational'] = $this->_number;
		} else {
			return $this->cache['rational'] = $this->_number->toBigRational();
		}
	}


	/**
	 * Return number converted to fraction.
	 * For example `2.5` will be converted to `[5, 2]`.
	 * The fraction is always shortened to the basic shape.
	 * TIP: Use toBigRational() method instead for faster first result (limited functionality)
	 *
	 * @param bool $simplify Simplify fraction on output (null means to not simplify rational input, else simplify)
	 * @return FractionNumbersOnly
	 */
	public function toFraction(?bool $simplify = null): FractionNumbersOnly
	{
		$simplify = ($simplify === true || ($simplify === null && !($this->_number instanceof BigRational)));

		if ($this->cache[$simplify ? 'fractionSimplified' : 'fraction']) {
			return clone $this->cache[$simplify ? 'fractionSimplified' : 'fraction'];
		}

		$rationalNumber = $this->toBigRational($simplify);

		return clone($this->cache[$simplify ? 'fractionSimplified' : 'fraction'] = new FractionNumbersOnly($rationalNumber->getNumerator(), $rationalNumber->getDenominator()));
	}


	/**
	 * Returns a number in computer readable form (in LaTeX format).
	 *
	 * @return MathLatexBuilder
	 */
	public function toLatex(): MathLatexBuilder
	{
		if ($this->cache['latex'] !== null) {
			return $this->cache['latex'];
		} elseif ($this->_number instanceof BigRational) {
			return $this->cache['latex'] = RationalToLatex::convert($this->toBigRational(false));
		} elseif ($this->_number instanceof BigDecimal) {
			return $this->cache['latex'] = MathLatexToolkit::create((string) $this->_number);
		} else {
			return $this->cache['latex'] = MathLatexToolkit::create((string) $this->_number);
		}
	}


	/**
	 * Returns a number in human readable form (valid SmartNumber input).
	 *
	 * @return MathHumanStringBuilder
	 */
	public function toHumanString(): MathHumanStringBuilder
	{
		if ($this->cache['humanString'] !== null) {
			return $this->cache['humanString'];
		} elseif ($this->_number instanceof BigRational) {
			return $this->cache['humanString'] = RationalToHumanString::convert($this->toBigRational(false));
		} else {
			return $this->cache['humanString'] = MathHumanStringToolkit::create((string) $this->_number);
		}
	}


	/**
	 * Detects that the number passed is integer.
	 * Advanced methods through fractional truncation are used for detection.
	 *
	 * @return bool
	 */
	public function isInteger(): bool
	{
		try {
			$this->_number->toScale(0);

			return true;
		} catch (RoundingNecessaryException $e) {
		}

		return false;
	}


	/**
	 * Returns number represented by string (valid SmartNumber input)
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) $this->toHumanString();
	}


	/**
	 * @param int|float|string|BigNumber $input
	 * @throws \Mathematicator\Numbers\Exception\NumberFormatException
	 * @throws \Mathematicator\Numbers\Exception\DivisionByZeroException
	 */
	protected function setValue($input): void
	{
		try {
			$this->_number = BigNumber::of($input);
		} catch (NumberFormatException $e) {
			throw new \Mathematicator\Numbers\Exception\NumberFormatException($e->getMessage());
		} catch (DivisionByZeroException $e) {
			throw new \Mathematicator\Numbers\Exception\DivisionByZeroException($e->getMessage());
		}
	}


	/**
	 * Invalidates internal cache used for faster reading.
	 */
	protected function invalidateCache(): void
	{
		$this->cache = [
			'float' => null,
			'fraction' => null,
			'fractionSimplified' => null,
			'humanString' => null,
			'latex' => null,
			'rational' => null,
			'rationalSimplified' => null,
		];
	}


	/**
	 * Returns rational number in normal form
	 *
	 * @return BigRational
	 */
	private function toBigRationalSimplified(): BigRational
	{
		if ($this->cache['rationalSimplified']) {
			return $this->cache['rationalSimplified'];
		}
		if ($this->_number instanceof BigRational) {
			return $this->cache['rationalSimplified'] = $this->_number->simplified();
		}

		return $this->cache['rationalSimplified'] = $this->_number->toBigRational()->simplified();
	}
}

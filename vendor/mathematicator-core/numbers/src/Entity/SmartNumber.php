<?php

declare(strict_types=1);

namespace Mathematicator\Numbers;


use Mathematicator\Engine\DivisionByZero;
use Mathematicator\Engine\MathematicatorException;
use Nette\SmartObject;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

class SmartNumber
{

	use SmartObject;

	/**
	 * @var int
	 */
	private $accuracy;

	/**
	 * @var string
	 */
	private $input;

	/**
	 * @var string
	 */
	private $string;

	/**
	 * @var string
	 */
	private $integer;

	/**
	 * @var float
	 */
	private $float;

	/**
	 * @var string[]
	 */
	private $fraction = [];

	/**
	 * @param int|null $accuracy
	 * @param string $number
	 * @throws NumberException|MathematicatorException
	 */
	public function __construct(?int $accuracy, string $number)
	{
		$this->accuracy = $accuracy ?? 100;
		$this->setValue($number);
	}

	/**
	 * @return string
	 */
	public function getInput(): string
	{
		return $this->input;
	}

	/**
	 * @return string
	 */
	public function getInteger(): string
	{
		return $this->integer;
	}

	/**
	 * @return int
	 */
	public function getAbsoluteInteger(): int
	{
		return \abs($this->integer);
	}

	/**
	 * @return float
	 */
	public function getFloat(): float
	{
		return (float) $this->float;
	}

	/**
	 * @return string
	 */
	public function getFloatString(): string
	{
		return (string) $this->float;
	}

	/**
	 * @return string[]|int[]
	 */
	public function getFraction(): array
	{
		return $this->fraction;
	}

	/**
	 * @return bool
	 */
	public function isInteger(): bool
	{
		return $this->integer !== null && ($this->input === $this->integer || $this->getFraction()[1] === 1);
	}

	/**
	 * @return bool
	 */
	public function isFloat(): bool
	{
		return $this->isInteger() === false && $this->integer !== null;
	}

	/**
	 * @return bool
	 */
	public function isPositive(): bool
	{
		return $this->float > 0;
	}

	/**
	 * @return bool
	 */
	public function isNegative(): bool
	{
		return $this->float < 0;
	}

	/**
	 * @return bool
	 */
	public function isZero(): bool
	{
		return $this->float === 0 || $this->float === 0.0;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->getString();
	}

	/**
	 * @return string
	 */
	public function getString(): string
	{
		if ($this->isInteger()) {
			return $this->integer;
		}

		$fraction = $this->getFraction();

		return '\frac{' . $fraction[0] . '}{' . $fraction[1] . '}';
	}

	/**
	 * @return string
	 */
	public function getHumanString(): string
	{
		if ($this->isInteger()) {
			return $this->integer;
		}

		$fraction = $this->getFraction();

		return $fraction[0] . '/' . $fraction[1];
	}

	/**
	 * @internal
	 * @param string $value
	 * @throws NumberException|DivisionByZero|MathematicatorException
	 */
	public function setValue(string $value): void
	{
		$value = preg_replace('/(\d)\s+(\d)/', '$1$2', $value);
		$value = rtrim(preg_replace('/^(\d*\.\d*?)0+$/', '$1', $value), '.');
		$this->input = $value;

		if (Validators::isNumeric($value)) {
			$toInteger = preg_replace('/\..*$/', '', $value);
			if (Validators::isNumericInt($value)) {
				$this->integer = $toInteger;
				$this->float = $toInteger * 1;
				$this->setStringHelper($toInteger);
				$this->fraction = [$toInteger, '1'];
			} else {
				$this->integer = $toInteger;
				$this->float = $value * 1;
				$this->setStringHelper($value);
				$this->setFractionHelper($value);
			}
		} elseif (preg_match('/^(?<mantissa>-?\d*[.]?\d+)(e|E|^)(?<exponent>-?\d*[.]?\d+)$/', $value, $parseExponential)) {
			$toString = bcmul($parseExponential['mantissa'], bcpow('10', $parseExponential['exponent'], $this->accuracy), $this->accuracy);
			$this->setStringHelper($toString);
			if (Strings::contains($toString, '.')) {
				$floatPow = $parseExponential['mantissa'] * (10 ** $parseExponential['exponent']);
				$this->integer = preg_replace('/\..+$/', '', $toString);
				$this->float = $floatPow;
				$this->setFractionHelper((string) $floatPow);
			} else {
				$this->integer = $toString;
				$this->float = $toString;
				$this->fraction = [$toString, '1'];
			}
		} elseif (preg_match('/^(?<x>-?\d*[.]?\d+)\s*\/\s*(?<y>-?\d*[.]?\d+)$/', $value, $parseFraction)) {
			$short = $this->shortFractionHelper($parseFraction['x'], $parseFraction['y']);
			$this->fraction = [$short[0], $short[1]];
			$this->float = $short[0] / $short[1];
			$this->integer = (string) (int) $this->float;
			$this->setStringHelper(bcdiv((string) $short[0], (string) $short[1], $this->accuracy));
		} else {
			throw new NumberException('Invalid input format. "' . $value . '" given.');
		}
	}

	/**
	 * @param string $string
	 */
	private function setStringHelper(string $string): void
	{
		$this->string = $string;

		if (preg_match('/^(?<int>.*)(\.|\,)(?<float>.+?)0+$/', $string, $redundantZeros)) {
			$this->string = $redundantZeros['int'] . '.' . $redundantZeros['float'];
		}
	}

	/**
	 * @param string $float
	 * @param float $tolerance
	 * @return int[]
	 * @throws NumberException|MathematicatorException
	 */
	private function setFractionHelper(string $float, float $tolerance = 1.e-8): array
	{
		if (preg_match('/^0+(\.0+)?$/', $float)) {
			return $this->fraction = ['0', '1'];
		}

		$floatOriginal = $float;
		$float = preg_replace('/^-/', '', $float);

		if ($float >= $tolerance) {
			$numerator = 1;
			$subNumerator = 0;
			$denominator = 0;
			$subDenominator = 1;
			$b = 1 / $float;
			do {
				$b = $b <= $tolerance ? 0 : 1 / $b;
				$a = floor($b);
				$aux = $numerator;
				$numerator = $a * $numerator + $subNumerator;
				$subNumerator = $aux;
				$aux = $denominator;
				$denominator = $a * $denominator + $subDenominator;
				$subDenominator = $aux;
				$b -= $a;
			} while ($denominator > 0 && abs($float - $numerator / $denominator) > $float * $tolerance);
		} elseif (preg_match('/^(.*)\.(.*)$/', $float, $floatParser)) {
			$numerator = ltrim($floatParser[1] . $floatParser[2], '0');
			$denominator = '1' . str_repeat('0', \strlen($floatParser[2]));
		} else {
			$numerator = str_replace('.', '', $float);
			$denominator = '1';
		}

		$short = $this->shortFractionHelper(number_format($numerator, 0, '.', ''), number_format($denominator, 0, '.', ''));

		if ((\is_int($short) || \is_float($short)) && $short[1] === null) {
			return $this->fraction = [(string) (int) $short, '1'];
		}

		if ($short[1] === null) {
			throw new NumberException('Part of fraction is NULL');
		}

		return $this->fraction = [
			($floatOriginal < 0 ? '-' : '') . $short[0],
			(string) $short[1],
		];
	}

	/**
	 * @param string $x
	 * @param string $y
	 * @param int $level
	 * @return int[]|string[]
	 * @throws DivisionByZero|MathematicatorException
	 */
	private function shortFractionHelper(string $x, string $y, int $level = 0): array
	{
		if ($y === 0 || preg_match('/^0+(\.0+)?$/', $y)) {
			throw new DivisionByZero(
				'Can not division fraction [' . $x . ' / ' . $y . '] by zero.',
				500, null, [$x, $y]
			);
		}

		if (!Validators::isNumericInt($x) || !Validators::isNumericInt($y)) {
			return $this->setFractionHelper((string) ($x / $y));
		}

		$originalX = $x;
		$x = number_format(abs((float) $x), 6, '.', '');
		$y = number_format(abs((float) $y), 6, '.', '');

		if ($level > 100) {
			return [$x, $y];
		}

		if ($x % $y === 0) {
			return [$x / $y, 1];
		}

		foreach (Cache::primaries() as $primary) {
			if ($primary > $x || $primary > $y) {
				break;
			}

			if ($x % $primary === 0 && $y % $primary === 0) {
				return $this->shortFractionHelper(
					(string) ($originalX / $primary),
					(string) ($y / $primary),
					$level + 1
				);
			}
		}

		return [($originalX < 0 ? '-' : '') . number_format((float) $x, 0, '.', ''), number_format((float) $y, 0, '.', '')];
	}

}

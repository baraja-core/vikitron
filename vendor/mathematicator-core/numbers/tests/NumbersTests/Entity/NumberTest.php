<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Entity;


use Brick\Math\RoundingMode;
use InvalidArgumentException;
use Mathematicator\Numbers\Entity\Number;
use Mathematicator\Numbers\Exception\DivisionByZeroException;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Mathematicator\Numbers\Helper\NumberHelper;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class NumberTest extends TestCase
{
	public function testInt(): void
	{
		$number = Number::of('10');
		Assert::same('10', (string) $number->toBigInteger());
	}


	public function testDecimal(): void
	{
		$number = Number::of('10.125');
		Assert::same('10', (string) $number->toBigInteger());
		Assert::same(10.125, $number->toFloat());
		Assert::same('10.125', (string) $number->toBigDecimal());
	}


	public function testDecimal2(): void
	{
		$number = Number::of('80.500');

		// Fractions
		Assert::same('161', (string) $number->toFraction()->getNumerator());
		Assert::same('2', (string) $number->toFraction()->getDenominator());

		// Outputs
		Assert::same(80.5, $number->toFloat());
		Assert::same('80', (string) $number->toBigInteger());
		Assert::same('80.500', (string) $number->toHumanString());
		Assert::same('161/2', (string) $number->toFraction());
		Assert::same('80.500', (string) $number);
		Assert::same('80.500', (string) $number->toLatex());

		// Operations
		Assert::same('322.000', (string) $number->toBigDecimal()->multipliedBy(4));
		Assert::same('-322.000', (string) $number->toBigDecimal()->multipliedBy(-4));
		Assert::same(322, $number->toBigDecimal()->multipliedBy(4)->abs()->toInt());
	}


	public function testPreFormattingWithCustomSeparators(): void
	{
		$number = Number::of(NumberHelper::preprocessInput('10x000a80g500', ['g', '.'], ['', 'a', 'x', 'd']));
		Assert::same('1000080.5', (string) $number->toBigDecimal());
		Assert::same('1000081', (string) $number->toBigDecimal()->toScale(0, RoundingMode::HALF_UP));
	}


	/**
	 * @throws InvalidArgumentException
	 */
	public function testPreFormattingWithCustomSeparators2(): void
	{
		$number = Number::of(NumberHelper::preprocessInput('10x000a80g500', ['g', 1], ['', 'a', 'x', 'd']));
	}


	public function testFractionPropertyClonability(): void
	{
		$number = Number::of('1000080.500');
		$fraction = $number->toFraction();
		Assert::same('2000161/2', (string) $number->toFraction());

		$newFraction = $fraction->setNumerator(1);
		Assert::same('2000161/2', (string) $number->toFraction());
		Assert::same('1/2', (string) $newFraction);
		Assert::same('1', (string) $newFraction->getNumerator());
	}


	public function testReadmeExample(): void
	{
		$number = Number::of('80.500');
		Assert::same('80.500', (string) $number->toBigDecimal());
		Assert::same('161', (string) $number->toFraction()->getNumerator());
		Assert::same('2', (string) $number->toFraction()->getDenominator());
		Assert::same('-322.000', (string) $number->toBigDecimal()->multipliedBy(-4));
		Assert::same('322', (string) $number->toBigDecimal()->multipliedBy(-4)->abs()->toInt());
		Assert::same('81', (string) $number->toBigDecimal()->toScale(0, RoundingMode::HALF_UP));

		$number2 = Number::of('161/2');
		Assert::same('161/2', (string) $number2->toHumanString());
		Assert::same('161/2+5=90.5', (string) $number2->toHumanString()->plus(5)->equals('90.5'));
		Assert::same('\frac{161}{2}', (string) $number2->toLatex());
		Assert::same('80.5', (string) $number2->toBigDecimal());
	}


	public function testBenchmarkCases(): void
	{
		$number = Number::of('158985102');
		Assert::same('158985102', (string) $number->toFraction()[0]);

		$number = Number::of('1482002/10');
		Assert::same('1482002', (string) $number->toFraction(false)->getNumerator());

		$number = Number::of('1482002/10');
		Assert::same('741001', (string) $number->toBigRational(true)->getNumerator());
	}


	public function testNonStandardInputs()
	{
		$number = Number::of('-100/25');
		Assert::same('-100/25', (string) $number->toBigRational());
		Assert::same('-4', (string) $number->toBigInteger());
		$number = Number::of('+5');
		Assert::same('5', (string) $number->toBigDecimal());
		$number = Number::of('+10/2');
		Assert::same('5', (string) $number->toBigDecimal());
		$number = Number::of('3.12e+2');
		Assert::same('312', (string) $number->toBigDecimal());
		$number = Number::of('3.12e+2');
		Assert::same('312', (string) $number->toBigDecimal());
		$number = Number::of('312e-2');
		Assert::same('3.12', (string) $number->toBigDecimal());
		$number = Number::of('1.5E-10');
		Assert::same('0.00000000015', (string) $number->toBigDecimal());
		$number = Number::of('-1.5E-10');
		Assert::same('-0.00000000015', (string) $number->toBigDecimal());
		Assert::same('-1.5E-10', (string) $number->getInput());
		$number = Number::of('3.0012e2');
		Assert::same('300.12', (string) $number->toBigDecimal());
	}


	public function testInfDecNumberFromRational()
	{
		$number = Number::of('1/3');
		Assert::same('0.333', (string) $number->toBigDecimal(3));
		Assert::same('0.334', (string) $number->toBigDecimal(3, RoundingMode::UP));
		Assert::same('0', (string) $number->toBigInteger());

		$number = Number::of('4/3');
		Assert::same('1', (string) $number->toBigInteger());
	}


	public function testDivisionByZero()
	{
		Assert::throws(function () {
			return Number::of('4/0');
		}, DivisionByZeroException::class);
	}


	public function testInvalidInputs()
	{
		Assert::throws(function () {
			return Number::of('');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('25....');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('++10/2');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('-+10/2');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('--10/2');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('--(10/2)');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('10.2/6.4');
		}, NumberFormatException::class);
		Assert::throws(function () {
			return Number::of('foo');
		}, NumberFormatException::class);
	}
}

(new NumberTest())->run();

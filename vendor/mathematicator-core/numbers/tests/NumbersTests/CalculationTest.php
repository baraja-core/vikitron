<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests;


use Brick\Math\RoundingMode;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\Entity\Number;
use Mathematicator\Numbers\SmartNumber;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../Bootstrap.php';

class CalculationTest extends TestCase
{
	public function testDecimal2(): void
	{
		$number = Number::of('80.500');

		// Operations
		Assert::same('322.000', (string) Calculation::of($number)->multipliedBy(4));
		Assert::same('-322.000', (string) Calculation::of($number)->multipliedBy(-4));
		Assert::same(322, Calculation::of($number)->multipliedBy(4)->abs()->getResult()->toBigInteger()->toInt());
	}


	public function testReadmeExample(): void
	{
		$number = Number::of('80.500');
		Assert::same('80.500', (string) $number->toBigDecimal());
		Assert::same('161', (string) $number->toFraction()->getNumerator());
		Assert::same('2', (string) $number->toFraction()->getDenominator());
		Assert::same('-322.000', (string) Calculation::of($number)->multipliedBy(-4));
		Assert::same('322', (string) Calculation::of($number)->multipliedBy(-4)->abs()->getResult()->toBigInteger()->toInt());
		Assert::same('81', (string) $number->toBigDecimal()->toScale(0, RoundingMode::HALF_UP));

		$number2 = Number::of('161/2');
		Assert::same('161/2', (string) $number2->toHumanString());
		Assert::same('161/2+5=90.5', (string) $number2->toHumanString()->plus(5)->equals('90.5'));
		Assert::same('\frac{161}{2}', (string) $number2->toLatex());
		Assert::same('80.5', (string) $number2->toBigDecimal());
	}


	public function testCalculationOperationWithNumberType(): void
	{
		$number1 = Number::of('80.500');
		$number2 = Number::of('2');
		Assert::same('161.000', (string) Calculation::of($number1)->multipliedBy($number2));
	}


	public function testCalculationOperationWithSmartNumberType(): void
	{
		$number1 = Number::of('80.500');
		$number2 = SmartNumber::of('2');
		Assert::same('161.000', (string) Calculation::of($number1)->multipliedBy($number2));
	}


	public function testNegativePower(): void
	{
		$number1 = Number::of('100');
		$number2 = SmartNumber::of('-2');
		Assert::same('0.0001', (string) Calculation::of($number1)->power($number2->getNumber(), 4));
	}


	public function testZeroPower(): void
	{
		$number1 = Number::of('100');
		$number2 = SmartNumber::of('0');
		Assert::same('1', (string) Calculation::of($number1)->power($number2->getNumber()));
	}


	public function testDivideIntPrecision(): void
	{
		$number1 = Number::of('5');
		$number2 = SmartNumber::of('2');
		Assert::same('2.5', (string) Calculation::of($number1)->dividedBy($number2->getNumber())->getResult()->toBigDecimal());
	}


	public function testMultipliedByDecimal(): void
	{
		$number1 = Number::of('81');
		$number2 = SmartNumber::of('0.5');

		Assert::same('81/2', (string) Calculation::of($number1)->multipliedBy($number2)->getResult()->toBigRational()->simplified());
	}


	public function testMultipliedWithRational(): void
	{
		$number1 = Number::of('81');
		$number2 = Number::of('1/3');
		Assert::same('81/3', (string) Calculation::of($number1)->multipliedBy($number2));

		$number1 = Number::of('10');
		$number2 = Number::of('1/3');
		Assert::same('3.333', (string) Calculation::of($number1)->multipliedBy($number2)->getResult()->toBigDecimal(3, RoundingMode::DOWN));
		Assert::same('10/3', (string) Calculation::of($number1)->multipliedBy($number2));

		$number1 = Number::of('1/3');
		$number2 = Number::of('10');
		Assert::same('10/3', (string) Calculation::of($number1)->multipliedBy($number2));

		$number1 = Number::of('1/3');
		$number2 = Number::of('1/3');
		Assert::same('1/9', (string) Calculation::of($number1)->multipliedBy($number2));
	}
}

(new CalculationTest())->run();

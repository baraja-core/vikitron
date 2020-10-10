<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests;


use Brick\Math\RoundingMode;
use InvalidArgumentException;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\Entity\Number;
use Mathematicator\Numbers\Exception\DivisionByZeroException;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Mathematicator\Numbers\Helper\NumberHelper;
use Mathematicator\Numbers\SmartNumber;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../Bootstrap.php';

class SmartNumberTest extends TestCase
{
	public function testInt(): void
	{
		$smartNumber = SmartNumber::of('10');
		Assert::same('10', (string) $smartNumber->toBigInteger());
	}


	public function testDecimal(): void
	{
		$smartNumber = SmartNumber::of('10.125');
		Assert::same('10', (string) $smartNumber->toBigInteger());
		Assert::same(10.125, $smartNumber->toFloat());
		Assert::same('10.125', (string) $smartNumber->toBigDecimal());
	}


	public function testDecimal2(): void
	{
		$smartNumber = SmartNumber::of('80.500');

		// Positivity
		Assert::same(true, $smartNumber->isPositive());
		Assert::same(false, $smartNumber->isNegative());
		Assert::same(false, $smartNumber->isZero());

		// Fractions
		Assert::same('161', (string) $smartNumber->toFraction()->getNumerator());
		Assert::same('2', (string) $smartNumber->toFraction()->getDenominator());

		// Outputs
		Assert::same(80.5, $smartNumber->toFloat());
		Assert::same('80', (string) $smartNumber->toBigInteger());
		Assert::same('80.500', (string) $smartNumber->toHumanString());
		Assert::same('161/2', (string) $smartNumber->toFraction());
		Assert::same('80.500', (string) $smartNumber);
		Assert::same('80.500', (string) $smartNumber->toLatex());

		// Operations
		Assert::same('322.000', (string) Calculation::of($smartNumber)->multipliedBy(4));
		Assert::same('-322.000', (string) Calculation::of($smartNumber)->multipliedBy(-4));
		Assert::same(322, Calculation::of($smartNumber)->multipliedBy(4)->abs()->getResult()->toInt());
	}


	public function testPreFormatting(): void
	{
		$smartNumber = SmartNumber::of('10 000 80.500');
		Assert::same('1000080.5', (string) $smartNumber->toBigDecimal());
	}


	public function testPreFormattingWithCustomSeparators(): void
	{
		$smartNumber = SmartNumber::of(NumberHelper::preprocessInput('10x000a80g500', ['g', '.'], ['', 'a', 'x', 'd']));
		Assert::same('1000080.5', (string) $smartNumber->toBigDecimal());
		Assert::same('1000081', (string) $smartNumber->toBigDecimal()->toScale(0, RoundingMode::HALF_UP));
	}


	/**
	 * @throws InvalidArgumentException
	 */
	public function testPreFormattingWithCustomSeparators2(): void
	{
		$smartNumber = SmartNumber::of(NumberHelper::preprocessInput('10x000a80g500', ['g', 1], ['', 'a', 'x', 'd']));
	}


	public function testFractionPropertyClonability(): void
	{
		$smartNumber = SmartNumber::of('10 000 80.500');
		$fraction = $smartNumber->toFraction();
		Assert::same('2000161/2', (string) $smartNumber->toFraction());

		$newFraction = $fraction->setNumerator(1);
		Assert::same('2000161/2', (string) $smartNumber->toFraction());
		Assert::same('1/2', (string) $newFraction);
		Assert::same('1', (string) $newFraction->getNumerator());
	}


	public function testReadmeExample(): void
	{
		$smartNumber = SmartNumber::of('80.500');
		Assert::same('80.500', (string) $smartNumber->toBigDecimal());
		Assert::same('161', (string) $smartNumber->toFraction()->getNumerator());
		Assert::same('2', (string) $smartNumber->toFraction()->getDenominator());
		Assert::same('-322.000', (string) Calculation::of($smartNumber)->multipliedBy(-4));
		Assert::same('322', (string) Calculation::of($smartNumber)->multipliedBy(-4)->abs()->getResult()->toInt());
		Assert::same('81', (string) $smartNumber->toBigDecimal()->toScale(0, RoundingMode::HALF_UP));

		$smartNumber2 = SmartNumber::of('161/2');
		Assert::same('161/2', (string) $smartNumber2->toHumanString());
		Assert::same('161/2+5=90.5', (string) $smartNumber2->toHumanString()->plus(5)->equals('90.5'));
		Assert::same('\frac{161}{2}', (string) $smartNumber2->toLatex());
		Assert::same('80.5', (string) $smartNumber2->toBigDecimal());
	}


	public function testBenchmarkCases(): void
	{
		$smartNumber = SmartNumber::of('158985102');
		Assert::same('158985102', (string) $smartNumber->toFraction()[0]);

		$smartNumber = SmartNumber::of('1482002/10');
		Assert::same('1482002', (string) $smartNumber->toFraction(false)->getNumerator());

		$smartNumber = SmartNumber::of('1482002/10');
		Assert::same('741001', (string) $smartNumber->toBigRational(true)->getNumerator());
	}


	public function testNonStandardInputs()
	{
		$smartNumber = SmartNumber::of('25....');
		Assert::same('25', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('4.');
		Assert::same('4', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('-100/25');
		Assert::same('-100/25', (string) $smartNumber->toBigRational());
		Assert::same('-4', (string) $smartNumber->toBigInteger());
		$smartNumber = SmartNumber::of('+4.');
		Assert::same('4', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('+5');
		Assert::same('5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('+10/2');
		Assert::same('5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('++10/2');
		Assert::same('5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('-+10/2');
		Assert::same('-5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('--10/2');
		Assert::same('5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('--(10/2)');
		Assert::same('5', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('3.12e+2');
		Assert::same('312', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('3.12e+2');
		Assert::same('312', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('312e-2');
		Assert::same('3.12', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('1.5E-10');
		Assert::same('0.00000000015', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('-1.5E-10');
		Assert::same('-0.00000000015', (string) $smartNumber->toBigDecimal());
		Assert::same('-1.5E-10', (string) $smartNumber->getInput());
		$smartNumber = SmartNumber::of('3.0012e2');
		Assert::same('300.12', (string) $smartNumber->toBigDecimal());
		$smartNumber = SmartNumber::of('10.2/6.4');
		Assert::same('102/64', (string) $smartNumber->toBigRational());
	}


	public function testInfDecNumberFromRational()
	{
		$smartNumber = SmartNumber::of('1/3');
		Assert::same('0.333', (string) $smartNumber->toBigDecimal(3));
		Assert::same('0.334', (string) $smartNumber->toBigDecimal(3, RoundingMode::UP));
		Assert::same('0', (string) $smartNumber->toBigInteger());

		$smartNumber = SmartNumber::of('4/3');
		Assert::same('1', (string) $smartNumber->toBigInteger());
	}


	public function testDivisionByZero()
	{
		Assert::throws(function () {
			return SmartNumber::of('4/0');
		}, DivisionByZeroException::class);
	}


	public function testBlankInput()
	{
		Assert::throws(function () {
			return SmartNumber::of('');
		}, NumberFormatException::class);
	}


	public function testCreateFromNumberEntity()
	{
		$smartNumber = SmartNumber::of(Number::of('1/3'));
		Assert::same('0.333', (string) $smartNumber->toBigDecimal(3));
		Assert::same('0.334', (string) $smartNumber->toBigDecimal(3, RoundingMode::UP));
		Assert::same('0', (string) $smartNumber->toBigInteger());
		Assert::same('0', (string) $smartNumber->toBigInteger());

		$smartNumber = SmartNumber::of(Number::of('4/3'));
		Assert::same('1', (string) $smartNumber->toBigInteger());

		$number = Number::of('1.0');
		$smartNumber = SmartNumber::of($number);
		Assert::same('1', (string) $smartNumber->toBigInteger());
		Assert::true($smartNumber instanceof SmartNumber);
		Assert::true($smartNumber instanceof Number);
		Assert::false($number instanceof SmartNumber);
		Assert::true($number instanceof Number);
	}
}

(new SmartNumberTest())->run();

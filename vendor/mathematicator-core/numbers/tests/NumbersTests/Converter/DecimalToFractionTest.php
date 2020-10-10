<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\DecimalToFraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class DecimalToFractionTest extends TestCase
{
	public function testUnderPrecision(): void
	{
		$fraction = DecimalToFraction::convert('0.0002', 3);
		Assert::same('1', (string) $fraction->getNumerator());
		Assert::same('5000', (string) $fraction->getDenominator());
	}


	public function testDecimal(): void
	{
		$fraction = DecimalToFraction::convert('1.5');
		Assert::same('3', (string) $fraction->getNumerator());
		Assert::same('2', (string) $fraction->getDenominator());
	}


	public function testInt(): void
	{
		$fraction = DecimalToFraction::convert('8');
		Assert::same('8', (string) $fraction->getNumerator());
		Assert::same('1', (string) $fraction->getDenominatorNotNull());
	}


	public function testBigDecimalNumber(): void
	{
		$fraction = DecimalToFraction::convert('80000000000000000000.0000000000000008');
		Assert::same('100000000000000000000000000000000001', (string) $fraction->getNumerator());
		Assert::same('1250000000000000', (string) $fraction->getDenominatorNotNull());
	}
}

(new DecimalToFractionTest())->run();

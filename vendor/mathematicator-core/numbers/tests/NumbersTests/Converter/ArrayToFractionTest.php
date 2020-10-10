<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Brick\Math\BigDecimal;
use Mathematicator\Numbers\Converter\ArrayToFraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class ArrayToFractionTest extends TestCase
{
	public function testSimpleFraction(): void
	{
		$fraction = ArrayToFraction::convert([10, '12']);
		Assert::same('10', $fraction->getNumerator());
		Assert::same('12', $fraction->getDenominator());
	}


	public function testCompoundFraction(): void
	{
		$fraction = ArrayToFraction::convert([[10, 5], ['12', BigDecimal::of('8.6')]]);
		Assert::same('10', $fraction->getNumerator()->getNumerator());
		Assert::same('5', $fraction->getNumerator()->getDenominator());
		Assert::same('12', $fraction->getDenominator()->getNumerator());
		Assert::same('8.6', $fraction->getDenominator()->getDenominator());
	}
}

(new ArrayToFractionTest())->run();

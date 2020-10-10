<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\FractionToArray;
use Mathematicator\Numbers\Entity\Fraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class FractionToArrayTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$arr = FractionToArray::convert(new Fraction(new Fraction(2, 8), '10'));
		Assert::same([['2', '8'], '10'], $arr);
	}
}

(new FractionToArrayTest())->run();

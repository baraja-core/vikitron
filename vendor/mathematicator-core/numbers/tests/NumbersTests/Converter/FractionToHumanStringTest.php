<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\FractionToHumanString;
use Mathematicator\Numbers\Entity\Fraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class FractionToHumanStringTest extends TestCase
{
	public function testSimpleFraction(): void
	{
		$result = FractionToHumanString::convert(new Fraction(5, '10'));
		Assert::same('5/10', (string) $result);
	}


	public function testCompoundFraction(): void
	{
		$result = FractionToHumanString::convert(new Fraction(new Fraction(2, 8), new Fraction(10, 15)));
		Assert::same('(2/8)/(10/15)', (string) $result);
	}
}

(new FractionToHumanStringTest())->run();

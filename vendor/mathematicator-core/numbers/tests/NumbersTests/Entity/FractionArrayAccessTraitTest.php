<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Entity;


use Mathematicator\Numbers\Entity\Fraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class FractionArrayAccessTraitTest extends TestCase
{
	public function testEntity(): void
	{
		$fraction = new Fraction(15, 19);
		Assert::same('15', $fraction[0]);
		Assert::same('19', $fraction[1]);
	}


	public function testEntity2(): void
	{
		$fraction = new Fraction(new Fraction(25, 1), 19);
		Assert::same('25', $fraction[0][0]);
		Assert::same('1', $fraction[0][1]);
		Assert::same('19', $fraction[1]);
	}


	public function testEntity3(): void
	{
		$fraction = new Fraction(new Fraction(25, null), 19);
		Assert::same('25', $fraction[0][0]);
		Assert::same('1', $fraction[0][1]);
		Assert::same('19', $fraction[1]);
	}


	public function testEntity4(): void
	{
		$fraction = new Fraction(new Fraction(25, null), 19);
		Assert::same('25', $fraction['numerator'][0]);
		Assert::same('1', $fraction[0][1]);
		Assert::same('19', $fraction['denominator']);
	}
}

(new FractionArrayAccessTraitTest())->run();

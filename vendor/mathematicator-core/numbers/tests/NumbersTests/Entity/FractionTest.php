<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Entity;


use Mathematicator\Numbers\Entity\Fraction;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class FractionTest extends TestCase
{
	public function testEntity(): void
	{
		$fraction = new Fraction(15, 19);
		Assert::same('15', $fraction->getNumerator());
		Assert::same('19', $fraction->getDenominator());
	}


	public function testEntity2(): void
	{
		$fraction = new Fraction(new Fraction(25, 1), 19);
		Assert::same('25', $fraction->getNumerator()->getNumerator());
		Assert::same('1', $fraction->getNumerator()->getDenominator());
		Assert::same('19', $fraction->getDenominator());
	}


	public function testEntity3(): void
	{
		$fraction = new Fraction(new Fraction(25, null), 19);
		Assert::same('25', $fraction->getNumerator()->getNumerator());
		Assert::same('19', $fraction->getNumerator()->getParent()->getDenominator());
		Assert::same('1', $fraction->getNumerator()->getDenominatorNotNull());
		Assert::same('19', $fraction->getDenominator());
	}
}

(new FractionTest())->run();

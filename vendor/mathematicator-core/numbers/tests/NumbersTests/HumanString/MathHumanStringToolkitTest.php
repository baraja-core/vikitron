<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\HumanString;


use Brick\Math\BigInteger;
use Mathematicator\Numbers\HumanString\MathHumanStringToolkit;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class MathHumanStringToolkitTest extends TestCase
{
	public function testPow(): void
	{
		$latex = MathHumanStringToolkit::pow(1, 2);
		Assert::same('1^(2)', (string) $latex);
	}


	public function testFrac(): void
	{
		$latex = MathHumanStringToolkit::frac(1, 2);
		Assert::same('1/2', (string) $latex);
	}


	public function testSqrt(): void
	{
		$latex = MathHumanStringToolkit::sqrt(1024, 3);
		Assert::same('sqrt[3](1024)', (string) $latex);
	}


	public function testRecreateAndBrickMathNumber(): void
	{
		$latex = MathHumanStringToolkit::create(
			MathHumanStringToolkit::frac(1, 2)
		)->multipliedBy(BigInteger::of('10000000000000000000000'));
		Assert::same('1/2*10000000000000000000000', (string) $latex);
	}
}

(new MathHumanStringToolkitTest())->run();

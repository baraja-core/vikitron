<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\HumanString;


use Brick\Math\BigInteger;
use Mathematicator\Numbers\HumanString\MathHumanStringToolkit;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class MathHumanStringBuilderTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$latex = MathHumanStringToolkit::frac(1, 2)->multipliedBy('10');
		Assert::same('1/2*10', (string) $latex);
	}


	public function testRecreateAndBrickMathNumber(): void
	{
		$latex = MathHumanStringToolkit::create(
			MathHumanStringToolkit::frac(1, 2)
		)->multipliedBy(BigInteger::of('10000000000000000000000'));
		Assert::same('1/2*10000000000000000000000', (string) $latex);
	}
}

(new MathHumanStringBuilderTest())->run();

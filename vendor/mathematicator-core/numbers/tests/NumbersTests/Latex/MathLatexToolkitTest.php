<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Latex;


use Brick\Math\BigInteger;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class MathLatexToolkitTest extends TestCase
{
	public function testPow(): void
	{
		$latex = MathLatexToolkit::pow(1, 2);
		Assert::same('{1}^{2}', (string) $latex);
	}


	public function testFrac(): void
	{
		$latex = MathLatexToolkit::frac(1, 2);
		Assert::same('\frac{1}{2}', (string) $latex);
	}


	public function testSqrt(): void
	{
		$latex = MathLatexToolkit::sqrt(1024, 3);
		Assert::same('\sqrt[3]{1024}', (string) $latex);
	}


	public function testRecreateAndBrickMathNumber(): void
	{
		$latex = MathLatexToolkit::create(
			MathLatexToolkit::frac(1, 2)
		)->multipliedBy(BigInteger::of('10000000000000000000000'));
		Assert::same('\frac{1}{2}\ \cdot\ 10000000000000000000000', (string) $latex);
	}
}

(new MathLatexToolkitTest())->run();

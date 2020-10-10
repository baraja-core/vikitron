<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Latex;


use Brick\Math\BigInteger;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class MathLatexBuilderTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$latex = MathLatexToolkit::frac(1, 2)->multipliedBy('10');
		Assert::same('\frac{1}{2}\ \cdot\ 10', (string) $latex);
	}


	public function testRecreateAndBrickMathNumber(): void
	{
		$latex = MathLatexToolkit::create(
			MathLatexToolkit::frac(1, 2)
		)->multipliedBy(BigInteger::of('10000000000000000000000'));
		Assert::same('\frac{1}{2}\ \cdot\ 10000000000000000000000', (string) $latex);
	}
}

(new MathLatexBuilderTest())->run();

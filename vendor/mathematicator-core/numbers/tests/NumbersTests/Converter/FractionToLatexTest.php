<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\FractionToLatex;
use Mathematicator\Numbers\Entity\Fraction;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class FractionToLatexTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$latex = FractionToLatex::convert(new Fraction(new Fraction(2, 8), '10'))->multipliedBy('9');
		Assert::same('\frac{\frac{2}{8}}{10}\ \cdot\ 9', (string) $latex);
	}
}

(new FractionToLatexTest())->run();

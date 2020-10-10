<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Tests\Helpers;


use Mathematicator\Calculator\Helpers\FractionHelper;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class FractionHelperTest extends TestCase
{

	/**
	 * @dataprovider getStringToSimpleFractionCases
	 * @param string[] $expected
	 * @param string $query
	 */
	public function testStringToSimpleFraction(array $expected, string $query): void
	{
		$result = FractionHelper::stringToSimpleFraction($query);
		Assert::same($expected[0], $result->getNumerator());
		Assert::same($expected[1], $result->getDenominator());
	}


	/**
	 * @dataprovider getFractionToLatexCases
	 * @param string $expected
	 * @param string $query
	 */
	public function testFractionToLatex(string $expected, string $query): void
	{
		$fraction = FractionHelper::stringToSimpleFraction($query);

		$result = FractionHelper::fractionToLatex($fraction);

		Assert::same($expected, $result);
	}


	/**
	 * @return string[]
	 */
	public function getStringToSimpleFractionCases(): array
	{
		return [
			[['2', '1'], '2/1'],
			[['sin(x)', '1.8'], 'sin(x)/1.8'],
			[['sin(x)', '1'], 'sin(x)'],
		];
	}


	/**
	 * @return string[]
	 */
	public function getFractionToLatexCases(): array
	{
		return [
			['\frac{2}{1}', '2/1'],
		];
	}
}

(new FractionHelperTest())->run();

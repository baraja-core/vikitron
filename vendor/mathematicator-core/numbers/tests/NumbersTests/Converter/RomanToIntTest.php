<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\RomanToInt;
use Mathematicator\Numbers\Exception\NumberFormatException;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class RomanToIntTest extends TestCase
{
	public function testConvert(): void
	{
		Assert::same('61', (string) RomanToInt::convert('LXI'));
		Assert::same('2601', (string) RomanToInt::convert('MMDCI'));
		Assert::same('28', (string) RomanToInt::convert('XXVIII'));
		Assert::same('4000', (string) RomanToInt::convert('MMMCMXCIXI')); // not optimal, but valid
		Assert::same('XXXII', (string) RomanToInt::reverse('32'));
	}


	/**
	 * @dataProvider getInvalidInputs
	 * @param string $input
	 */
	public function testInvalidInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			RomanToInt::convert($input);
		}, NumberFormatException::class);
	}


	/**
	 * @return string[]
	 */
	public function getInvalidInputs(): array
	{
		return [['0'], ['-1'], ['-X'], ['-I'], ['IMMMCMXCIXI']];
	}
}

(new RomanToIntTest())->run();

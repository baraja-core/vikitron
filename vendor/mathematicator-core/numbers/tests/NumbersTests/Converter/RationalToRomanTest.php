<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\RationalToRoman;
use Mathematicator\Numbers\Exception\OutOfSetException;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class RationalToRomanTest extends TestCase
{
	public function testConvert(): void
	{
		Assert::same('LXI', (string) RationalToRoman::convert('61'));
		Assert::same('MMDCI', (string) RationalToRoman::convert('2601'));
		Assert::same('XXVIII', (string) RationalToRoman::convert('28'));
		Assert::same('C', (string) RationalToRoman::convert('1e2'));
		Assert::same('C', (string) RationalToRoman::convert('1e2'));
		Assert::same('I', (string) RationalToRoman::convert('12/12'));
		Assert::same('X', (string) RationalToRoman::convert('120/12'));
		Assert::same('·', (string) RationalToRoman::convert('1/12'));
		Assert::same('S', (string) RationalToRoman::convert('6/12'));
		Assert::same('IS·', (string) RationalToRoman::convert('19/12'));
		Assert::same('MMMCMXCIX', (string) RationalToRoman::convert('3999'));
	}


	/**
	 * @dataProvider getOutOfSetInputs
	 * @param string $input
	 */
	public function testOutOfSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			RationalToRoman::convert($input);
		}, OutOfSetException::class);
	}


	/**
	 * @dataProvider getOutOfIntegerSetInputs
	 * @param string $input
	 */
	public function testOutOfIntegerSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			RationalToRoman::convert($input);
		}, OutOfSetException::class);
	}


	/**
	 * @return string[]
	 */
	public function getOutOfSetInputs(): array
	{
		return [['-1'], ['-256'], ['-9998123456'], ['-25.2'], ['1.3']];
	}


	/**
	 * @return string[]
	 */
	public function getOutOfIntegerSetInputs(): array
	{
		return [['-1'], ['-256'], ['-9998123456'], ['-25.2'], ['1.3']];
	}
}

(new RationalToRomanTest())->run();

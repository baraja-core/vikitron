<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\IntToRomanBasic;
use Mathematicator\Numbers\Exception\OutOfSetException;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class IntToRomanBasicTest extends TestCase
{
	public function testConvert(): void
	{
		Assert::same('LXI', (string) IntToRomanBasic::convert('61'));
		Assert::same('MMDCI', (string) IntToRomanBasic::convert('2601'));
		Assert::same('XXVIII', (string) IntToRomanBasic::convert('28'));
		Assert::same('21', (string) IntToRomanBasic::reverse('XXI'));
		Assert::same('C', (string) IntToRomanBasic::convert('1e2'));
		Assert::same('C', (string) IntToRomanBasic::convert('1e2'));
		Assert::same('MMMCMXCIX', (string) IntToRomanBasic::convert('3999'));

		Assert::same('MMMM', (string) IntToRomanBasic::convert('4000'));
		Assert::same('4000', (string) IntToRomanBasic::reverse('MMMM'));

		Assert::same('MMMMCMXC', (string) IntToRomanBasic::convert('4990'));
		Assert::same('4990', (string) IntToRomanBasic::reverse('MMMMCMXC'));

		Assert::same('MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMI', (string) IntToRomanBasic::convert('40001'));
	}


	/**
	 * @dataProvider getOutOfSetInputs
	 * @param string $input
	 */
	public function testOutOfSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			IntToRomanBasic::convert($input);
		}, OutOfSetException::class);
	}


	/**
	 * @dataProvider getOutOfIntegerSetInputs
	 * @param string $input
	 */
	public function testOutOfIntegerSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			IntToRomanBasic::convert($input);
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
		return [['-1'], ['-256'], ['-9998123456'], ['-25.2'], ['1/2'], ['1.3'], ['6/12'], ['15/12']];
	}
}

(new IntToRomanBasicTest())->run();

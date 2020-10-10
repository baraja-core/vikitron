<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Converter;


use Mathematicator\Numbers\Converter\IntToRoman;
use Mathematicator\Numbers\Exception\OutOfSetException;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class IntToRomanTest extends TestCase
{
	public function testConvert(): void
	{
		Assert::same('LXI', (string) IntToRoman::convert('61'));
		Assert::same('MMDCI', (string) IntToRoman::convert('2601'));
		Assert::same('XXVIII', (string) IntToRoman::convert('28'));
		Assert::same('21', (string) IntToRoman::reverse('XXI'));
		Assert::same('C', (string) IntToRoman::convert('1e2'));
		Assert::same('C', (string) IntToRoman::convert('1e2'));
		Assert::same('MMMCMXCIX', (string) IntToRoman::convert('3999'));
		Assert::same('_I_V', (string) IntToRoman::convert('4000'));
		Assert::same('_X_LI', (string) IntToRoman::convert('40001'));
		Assert::same('_C_M_L_V', (string) IntToRoman::convert('955000'));
		Assert::same('__X__C__I__X_C_M_L_VMDXCIX', (string) IntToRoman::convert('99956599'));
	}


	public function testConvertToLatex(): void
	{
		Assert::same('\\overline{C}\\overline{M}\\overline{L}\\overline{V}', (string) IntToRoman::convertToLatex('955000'));
		Assert::same('\\overline{\\overline{X}}\\overline{\\overline{C}}\\overline{\\overline{I}}\\overline{\\overline{X}}\\overline{C}\\overline{M}\\overline{L}\\overline{V}MDXCIX', (string) IntToRoman::convertToLatex('99956599'));
	}


	/**
	 * @dataProvider getOutOfSetInputs
	 * @param string $input
	 */
	public function testOutOfSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			IntToRoman::convert($input);
		}, OutOfSetException::class);
	}


	/**
	 * @dataProvider getOutOfIntegerSetInputs
	 * @param string $input
	 */
	public function testOutOfIntegerSetInputs(string $input): void
	{
		Assert::throws(function () use ($input) {
			IntToRoman::convert($input);
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

(new IntToRomanTest())->run();

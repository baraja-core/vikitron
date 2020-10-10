<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\Latex;


use Mathematicator\Numbers\Validator\RomanNumberBasicValidator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class RomanNumberBasicValidatorTest extends TestCase
{

	/**
	 * @dataProvider getValidInputs
	 * @param string $input
	 */
	public function testValidInputs(string $input): void
	{
		Assert::true(RomanNumberBasicValidator::validate($input));
	}


	/**
	 * @dataProvider getInvalidInputs
	 * @param string $input
	 */
	public function testInvalidInputs(string $input): void
	{
		Assert::false(RomanNumberBasicValidator::validate($input));
	}


	public function testZeroHandling(): void
	{
		Assert::true(RomanNumberBasicValidator::validate('N', true));
		Assert::false(RomanNumberBasicValidator::validate('N', false));
	}


	/**
	 * @dataProvider getOptimalInputs
	 * @param string $input
	 */
	public function testOptimalInputs(string $input): void
	{
		Assert::true(RomanNumberBasicValidator::isOptimal($input));
	}


	/**
	 * @dataProvider getNotOptimalInputs
	 * @param string $input
	 */
	public function testNotOptimalInputs(string $input): void
	{
		Assert::false(RomanNumberBasicValidator::isOptimal($input));
	}


	/**
	 * @return string[]
	 */
	public function getValidInputs(): array
	{
		return [['i'], ['x'], ['I'], ['XII'], ['L']];
	}


	/**
	 * @return string[]
	 */
	public function getInvalidInputs(): array
	{
		return [[''], ['a'], ['-X'], ['-I'], ['aMMMCMXCIXI']];
	}


	/**
	 * @return string[]
	 */
	public function getOptimalInputs(): array
	{
		return [['i'], ['I'], ['XII'], ['L']];
	}


	/**
	 * @return string[]
	 */
	public function getNotOptimalInputs(): array
	{
		return [['VIIIIIIIII'], ['XXXXXX']];
	}
}

(new RomanNumberBasicValidatorTest())->run();

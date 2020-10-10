<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Helper;


use Mathematicator\Engine\Helper\Czech;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class CzechTest extends TestCase
{

	/**
	 * @dataprovider getGetDateTestCases
	 * @param mixed[] $input
	 */
	public function testGetDate(string $expected, array $input): void
	{
		Assert::same($expected, Czech::getDate($input[0], $input[1]));
	}


	/**
	 * @dataprovider getInflectionTestCases
	 * @param array $input
	 */
	public function testInflection(string $expected, array $input): void
	{
		Assert::same($expected, Czech::inflection($input[0], $input[1]));
	}


	/**
	 * @dataprovider getInflectionExceptionTestCases
	 * @param array $input
	 */
	public function testInflectionException(string $expected, array $input): void
	{
		Assert::exception(function () use ($input) {
			return Czech::inflection($input[0], $input[1]);
		}, $expected);
	}


	/**
	 * @return string[]
	 */
	public function getGetDateTestCases(): array
	{
		return [
			['2. května 2020', [new \DateTime('2020-05-02 10:50:01'), false]],
			['2. květen 2020', [new \DateTime('2020-05-02 10:50:01'), true]],
		];
	}


	public function testCreateInstance(): void
	{
		Assert::exception(function () {
			new Czech;
		}, \Error::class);
	}


	public function testEmptyGetDate(): void
	{
		Assert::same(Czech::getDate(\time(), true), Czech::getDate(null, true));
	}


	/**
	 * @return string[]
	 */
	public function getInflectionTestCases(): array
	{
		return [
			['1 zájezd', [1, ['zájezd', 'zájezdy', 'zájezdů']]],
			['2 zájezdy', [2, ['zájezd', 'zájezdy', 'zájezdů']]],
			['10 zájezdů', [10, ['zájezd', 'zájezdy', 'zájezdů']]],
			['0 zájezdů', [0, ['zájezd', 'zájezdy', 'zájezdů']]],
			['-3 zájezdy', [-3, ['zájezd', 'zájezdy', 'zájezdů']]],
			['-5 zájezdů', [-5, ['zájezd', 'zájezdy', 'zájezdů']]],
		];
	}


	/**
	 * @return string[]
	 */
	public function getInflectionExceptionTestCases(): array
	{
		return [
			[\RuntimeException::class, [1, ['zájezdy', 'zájezdů']]],
		];
	}
}

(new CzechTest())->run();

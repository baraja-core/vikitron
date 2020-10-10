<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests;


use Mathematicator\Engine\NumberRewriter;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class NumberRewriterTest extends TestCase
{

	/** @var NumberRewriter */
	private $numberRewriter;


	public function __construct(Container $container)
	{
		$this->numberRewriter = $container->getByType(NumberRewriter::class);
	}


	/**
	 * @dataprovider getNumberRewriterToNumber
	 */
	public function testNumberRewriterToNumber(string $expected, string $query): void
	{
		Assert::same($expected, $this->numberRewriter->toNumber($query));
	}


	/**
	 * @return string[]
	 */
	public function getNumberRewriterToNumber(): array
	{
		return [
			['5', 'pět'],
			['2 a 3', 'dva a tři'],
		];
	}
}

(new NumberRewriterTest(Bootstrap::boot()))->run();

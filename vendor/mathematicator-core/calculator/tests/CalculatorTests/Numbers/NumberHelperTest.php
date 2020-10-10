<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Tests\Numbers;


use Mathematicator\Calculator\Numbers\NumberHelper;
use Mathematicator\Calculator\Tests\Bootstrap;
use Nette\Application\LinkGenerator;
use Nette\Caching\Storages\DevNullStorage;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class NumberHelperTest extends TestCase
{

	/** @var NumberHelper */
	private $numberHelper;


	public function __construct(Container $container)
	{
		/** @var LinkGenerator $linkGenerator */
		$linkGenerator = $container->getByType(LinkGenerator::class);

		$this->numberHelper = new NumberHelper($linkGenerator, new DevNullStorage);
	}


	/**
	 * @dataprovider getPfactorCases
	 * @param string[] $expected
	 * @param string $query
	 */
	public function testPfactor(array $expected, string $query): void
	{
		Assert::same($expected, $this->numberHelper->pfactor($query));
	}


	/**
	 * @return string[]
	 */
	public function getPfactorCases(): array
	{
		return [
			[['1'], '1'],
			[['2', '5'], '10'],
			[['2', '5'], '10'],
		];
	}
}

(new NumberHelperTest(Bootstrap::boot()))->run();

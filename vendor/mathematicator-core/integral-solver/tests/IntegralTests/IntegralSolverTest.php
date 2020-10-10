<?php

declare(strict_types=1);

namespace Mathematicator\Integral\Tests;


use Mathematicator\Integral\IntegralSolver;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class IntegralSolverTest extends TestCase
{

	/** @var IntegralSolver */
	private $integralSolver;


	public function __construct(Container $container)
	{
		$this->integralSolver = $container->getByType(IntegralSolver::class);
	}


	/**
	 * @dataProvider getQueries
	 * @param string $query
	 * @param string $result
	 */
	public function testOne(string $query, string $result): void
	{
		Assert::same($result, $this->integralSolver->process($query)->getResult());
	}


	/**
	 * @return string[][]
	 */
	public function getQueries(): array
	{
		return [
			['0', '+c'],
			['1', 'x+c'],
			['12', '12x+c'],
			['x', '(x^2)/2+c'],
			['1+x', 'x+(x^2)/2+c'],
		];
	}
}

(new IntegralSolverTest(Bootstrap::boot()))->run();

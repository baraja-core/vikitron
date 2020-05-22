<?php

declare(strict_types=1);

namespace Mathematicator\Integral\Test;


use App\Booting;
use Mathematicator\Integral\IntegralSolver;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../autoload.php';

class IntegralSolverTest extends TestCase
{

	/** @var IntegralSolver */
	private $integralSolver;


	/**
	 * @param IntegralSolver $integralSolver
	 */
	public function __construct(IntegralSolver $integralSolver)
	{
		$this->integralSolver = $integralSolver;
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
			['0', 'c'],
			['1', 'x+c'],
			['12', '12x+c'],
			['x', '(x^2)/2+c'],
			['1+x', 'x+(x^2)/2+c'],
		];
	}
}

if (isset($_SERVER['NETTE_TESTER_RUNNER'])) {
	$di = Booting::bootForTests()->createContainer();

	(new IntegralSolverTest(
		$di->getByType(IntegralSolver::class)
	))->run();
}
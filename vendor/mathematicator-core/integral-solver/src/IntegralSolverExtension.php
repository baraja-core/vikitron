<?php

declare(strict_types=1);

namespace Mathematicator\Integral;


use Mathematicator\Integral\Solver\Solver;
use Nette\DI\CompilerExtension;

final class IntegralSolverExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('integralSolver'))
			->setFactory(IntegralSolver::class);

		$builder->addDefinition($this->prefix('solver'))
			->setFactory(Solver::class);
	}
}

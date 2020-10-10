<?php

declare(strict_types=1);

namespace Mathematicator\Calculator;


use Mathematicator\Calculator\Equation\NewtonMethod;
use Mathematicator\Calculator\MathFunction\FunctionManager;
use Mathematicator\Calculator\MathFunction\Functions\AbsFunction;
use Mathematicator\Calculator\MathFunction\Functions\SinFunction;
use Mathematicator\Calculator\MathFunction\Functions\SqrtFunction;
use Mathematicator\Calculator\Numbers\NumberHelper;
use Mathematicator\Calculator\Operation\AddNumbers;
use Mathematicator\Calculator\Operation\BaseOperation;
use Mathematicator\Calculator\Operation\DivisionNumbers;
use Mathematicator\Calculator\Operation\Factorial;
use Mathematicator\Calculator\Operation\MultiplicationNumber;
use Mathematicator\Calculator\Operation\PowNumber;
use Mathematicator\Calculator\Operation\SubtractNumbers;
use Mathematicator\Calculator\Step\Controller\StepMultiplicationController;
use Mathematicator\Calculator\Step\Controller\StepPlusController;
use Mathematicator\Calculator\Step\Controller\StepPowController;
use Mathematicator\Calculator\Step\Controller\StepSinController;
use Mathematicator\Calculator\Step\Controller\StepSqrtController;
use Mathematicator\Calculator\Step\Controller\StepSqrtHelper;
use Mathematicator\Calculator\Step\Model\RomanIntSteps;
use Mathematicator\Calculator\Step\StepEndpoint;
use Mathematicator\Calculator\Step\StepFactory;
use Nette\DI\CompilerExtension;

final class CalculatorExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('calculator'))
			->setFactory(Calculator::class);

		$builder->addDefinition($this->prefix('tokensCalculator'))
			->setFactory(TokensCalculator::class);

		$builder->addDefinition($this->prefix('numberHelper'))
			->setFactory(NumberHelper::class);

		$builder->addDefinition($this->prefix('stepFactory'))
			->setFactory(StepFactory::class);

		$builder->addDefinition($this->prefix('newtonMethod'))
			->setFactory(NewtonMethod::class);

		// operations
		$builder->addDefinition($this->prefix('baseOperation'))
			->setFactory(BaseOperation::class);

		$builder->addDefinition($this->prefix('addNumbers'))
			->setFactory(AddNumbers::class);

		$builder->addDefinition($this->prefix('subtractNumbers'))
			->setFactory(SubtractNumbers::class);

		$builder->addDefinition($this->prefix('multiplicationNumber'))
			->setFactory(MultiplicationNumber::class);

		$builder->addDefinition($this->prefix('divisionNumbers'))
			->setFactory(DivisionNumbers::class);

		$builder->addDefinition($this->prefix('powNumber'))
			->setFactory(PowNumber::class);

		$builder->addDefinition($this->prefix('factorial'))
			->setFactory(Factorial::class);

		// steps
		$builder->addDefinition($this->prefix('stepEndpoint'))
			->setFactory(StepEndpoint::class);

		$builder->addDefinition($this->prefix('romanIntSteps'))
			->setFactory(RomanIntSteps::class);

		// step controllers
		$builder->addDefinition($this->prefix('stepPlusController'))
			->setFactory(StepPlusController::class)
			->setAutowired(StepPlusController::class);

		$builder->addDefinition($this->prefix('stepMultiplicationController'))
			->setFactory(StepMultiplicationController::class)
			->setAutowired(StepMultiplicationController::class);

		$builder->addDefinition($this->prefix('stepSqrtController'))
			->setFactory(StepSqrtController::class)
			->setAutowired(StepSqrtController::class);

		$builder->addDefinition($this->prefix('stepSqrtHelper'))
			->setFactory(StepSqrtHelper::class)
			->setAutowired(StepSqrtHelper::class);

		$builder->addDefinition($this->prefix('stepPowController'))
			->setFactory(StepPowController::class)
			->setAutowired(StepPowController::class);

		$builder->addDefinition($this->prefix('stepSinController'))
			->setFactory(StepSinController::class)
			->setAutowired(StepSinController::class);

		// functions
		$builder->addDefinition($this->prefix('functionManager'))
			->setFactory(FunctionManager::class);

		$builder->addDefinition($this->prefix('absFunction'))
			->setFactory(AbsFunction::class)
			->setAutowired(AbsFunction::class);

		$builder->addDefinition($this->prefix('sqrtFunction'))
			->setFactory(SqrtFunction::class)
			->setAutowired(SqrtFunction::class);

		$builder->addDefinition($this->prefix('sinFunction'))
			->setFactory(SinFunction::class)
			->setAutowired(SinFunction::class);
	}
}

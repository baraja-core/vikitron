<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Brick\Math\BigRational;
use Mathematicator\Calculator\Step\Controller\StepMultiplicationController;
use Mathematicator\Calculator\Step\StepFactory;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;

class MultiplicationNumber
{
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$result = Calculation::of($left->getNumber()->getNumber())
				->multipliedBy($right->getNumber()->getNumber())
				->getResult();
		} else {
			$leftFraction = $left->getNumber()->toBigRational();
			$rightFraction = $right->getNumber()->toBigRational();

			$result = SmartNumber::of(
				BigRational::nd(
					$leftFraction->getNumerator()->multipliedBy($rightFraction->getNumerator()),
					$leftFraction->getDenominator()->multipliedBy($rightFraction->getDenominator())
				)
			);
		}

		$newNumber = new NumberToken($result);
		$newNumber->setToken((string) $newNumber->getNumber())
			->setPosition($left->getPosition())
			->setType('number');

		return (new NumberOperationResult())
			->setNumber($newNumber)
			->setDescription(
				'Násobení čísel ' . $left->getNumber()->toHumanString() . ' * ' . $right->getNumber()->toHumanString()
			)
			->setAjaxEndpoint(
				StepFactory::getAjaxEndpoint(StepMultiplicationController::class, [
					'x' => $left->getNumber()->toHumanString(),
					'y' => $right->getNumber()->toHumanString(),
				])
			);
	}
}

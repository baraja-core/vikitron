<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Brick\Math\BigRational;
use Mathematicator\Calculator\Step\Controller\StepPlusController;
use Mathematicator\Calculator\Step\StepFactory;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;

final class AddNumbers
{
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		$leftNumber = $left->getNumber();
		$rightNumber = $right->getNumber();

		if ($leftNumber->isInteger() && $rightNumber->isInteger()) {
			$result = Calculation::of($leftNumber)
				->plus($rightNumber->getNumber())
				->getResult();
		} else {
			$leftFraction = $leftNumber->toBigRational();
			$rightFraction = $rightNumber->toBigRational();

			$result = SmartNumber::of(
				BigRational::nd(
					$rightFraction->getDenominator()->multipliedBy($leftFraction->getNumerator())
						->plus($leftFraction->getDenominator()->multipliedBy($rightFraction->getNumerator())),
					$leftFraction->getDenominator()->multipliedBy($rightFraction->getDenominator())
				)->simplified()
			);
		}

		$newNumber = new NumberToken($result);
		$newNumber
			->setToken((string) $newNumber->getNumber())
			->setPosition($left->getPosition())
			->setType('number');

		$_left = (string) $leftNumber->toHumanString();
		$_right = (string) $rightNumber->toHumanString();

		return (new NumberOperationResult())
			->setNumber($newNumber)
			->setDescription(
				'Sčítání čísel '
				. (strpos($_left, '-') === 0 ? '(' . $_left . ')' : $_left)
				. ' + '
				. (strpos($_right, '-') === 0 ? '(' . $_right . ')' : $_right)
			)
			->setAjaxEndpoint(
				StepFactory::getAjaxEndpoint(StepPlusController::class, [
					'x' => $leftNumber->toHumanString(),
					'y' => $rightNumber->toHumanString(),
				])
			);
	}
}

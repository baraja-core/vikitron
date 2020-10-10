<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\Entity\Query;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;

final class SubtractNumbers
{
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		$leftNumber = $left->getNumber();
		$rightNumber = $right->getNumber();

		if ($leftNumber->isInteger() && $rightNumber->isInteger()) {
			$result = Calculation::of($leftNumber)->minus($rightNumber->getNumber())->getResult();
		} else {
			$leftFraction = $leftNumber->toBigRational();
			$rightFraction = $rightNumber->toBigRational();

			$resultNumerator = $rightFraction->getDenominator()
				->multipliedBy($leftFraction->getNumerator())
				->minus($leftFraction->getDenominator()->multipliedBy($rightFraction->getNumerator()));

			$resultDenominator = $leftFraction->getDenominator()->multipliedBy($rightFraction->getDenominator());

			$result = SmartNumber::of($resultNumerator . '/' . $resultDenominator);
		}

		$newNumber = new NumberToken($result);
		$newNumber->setToken((string) $newNumber->getNumber())
			->setPosition($left->getPosition())
			->setType('number');

		return (new NumberOperationResult())
			->setNumber($newNumber)
			->setDescription(
				'Odčítání čísel ' . $leftNumber->toHumanString() . ' - ' . $rightNumber->toHumanString()
			);
	}
}

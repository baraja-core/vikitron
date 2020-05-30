<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\Query;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Step\Controller\StepMultiplicationController;
use Mathematicator\Step\StepFactory;
use Mathematicator\Tokenizer\Token\NumberToken;

class MultiplicationNumber
{

	/** @var NumberFactory */
	private $numberFactory;

	/** @var StepFactory */
	private $stepFactory;


	/**
	 * @param NumberFactory $numberFactory
	 * @param StepFactory $stepFactory
	 */
	public function __construct(NumberFactory $numberFactory, StepFactory $stepFactory)
	{
		$this->numberFactory = $numberFactory;
		$this->stepFactory = $stepFactory;
	}


	/**
	 * @param NumberToken $left
	 * @param NumberToken $right
	 * @param Query $query
	 * @return NumberOperationResult
	 */
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$result = bcmul($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $query->getDecimals());
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcmul($leftFraction[0], $rightFraction[0], $query->getDecimals()) . '/' . bcmul($leftFraction[1], $rightFraction[1], $query->getDecimals());
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		return (new NumberOperationResult)
			->setNumber($newNumber)
			->setDescription(
				'Násobení čísel ' . $left->getNumber()->getHumanString() . ' * ' . $right->getNumber()->getHumanString()
			)
			->setAjaxEndpoint(
				$this->stepFactory->getAjaxEndpoint(StepMultiplicationController::class, [
					'x' => $left->getNumber()->getHumanString(),
					'y' => $right->getNumber()->getHumanString(),
				])
			);
	}
}

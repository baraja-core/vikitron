<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\NumberToken;
use Model\Math\Step\Controller\StepMultiplicationController;
use Model\Math\Step\StepFactory;

class MultiplicationNumber
{

	/**
	 * @var NumberFactory
	 */
	private $numberFactory;

	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @var int
	 */
	private $tolerance;

	/**
	 * @param NumberFactory $numberFactory
	 * @param StepFactory $stepFactory
	 */
	public function __construct(NumberFactory $numberFactory, StepFactory $stepFactory)
	{
		$this->numberFactory = $numberFactory;
		$this->stepFactory = $stepFactory;
		$this->tolerance = 100;
	}

	public function process(NumberToken $left, NumberToken $right)
	{
		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$result = bcmul($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $this->tolerance);
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcmul($leftFraction[0], $rightFraction[0], $this->tolerance) . '/' . bcmul($leftFraction[1], $rightFraction[1], $this->tolerance);
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$result = new NumberOperationResult();
		$result->setNumber($newNumber);
		$result->setDescription(
			'Násobení čísel ' . $left->getNumber()->getHumanString() . ' * ' . $right->getNumber()->getHumanString()
		);
		$result->setAjaxEndpoint(
			$this->stepFactory->getAjaxEndpoint(StepMultiplicationController::class, [
				'x' => $left->getNumber()->getHumanString(),
				'y' => $right->getNumber()->getHumanString(),
			])
		);

		return $result;
	}

}

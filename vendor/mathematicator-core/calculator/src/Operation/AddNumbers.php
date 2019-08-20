<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\NumberToken;
use Model\Math\Step\Controller\StepPlusController;
use Model\Math\Step\StepFactory;

class AddNumbers
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

	/**
	 * @param NumberToken $left
	 * @param NumberToken $right
	 * @return NumberOperationResult
	 */
	public function process(NumberToken $left, NumberToken $right): NumberOperationResult
	{
		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$result = bcadd($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $this->tolerance);
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcadd(
					bcmul($rightFraction[1], $leftFraction[0], $this->tolerance),
					bcmul($leftFraction[1], $rightFraction[0], $this->tolerance),
					$this->tolerance
				) . '/' .
				bcmul($leftFraction[1], $rightFraction[1], $this->tolerance);

		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$_left = $left->getNumber()->getHumanString();
		$_right = $right->getNumber()->getHumanString();

		$result = new NumberOperationResult();
		$result->setNumber($newNumber);
		$result->setDescription(
			'Sčítání čísel '
			. ($_left[0] === '-' ? '(' . $_left . ')' : $_left)
			. ' + '
			. ($_right[0] === '-' ? '(' . $_right . ')' : $_right)
		);
		$result->setAjaxEndpoint(
			$this->stepFactory->getAjaxEndpoint(StepPlusController::class, [
				'x' => $left->getNumber()->getHumanString(),
				'y' => $right->getNumber()->getHumanString(),
			])
		);

		return $result;
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Search\Query;
use Mathematicator\Step\Controller\StepPlusController;
use Mathematicator\Step\StepFactory;
use Mathematicator\Tokenizer\Token\NumberToken;

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
			$result = bcadd($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $query->getDecimals());
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcadd(
					bcmul($rightFraction[1], $leftFraction[0], $query->getDecimals()),
					bcmul($leftFraction[1], $rightFraction[0], $query->getDecimals()),
					$query->getDecimals()
				) . '/' .
				bcmul($leftFraction[1], $rightFraction[1], $query->getDecimals());

		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$_left = $left->getNumber()->getHumanString();
		$_right = $right->getNumber()->getHumanString();

		return (new NumberOperationResult)
			->setNumber($newNumber)
			->setDescription(
				'Sčítání čísel '
				. (strpos($_left, '-') === 0 ? '(' . $_left . ')' : $_left)
				. ' + '
				. (strpos($_right, '-') === 0 ? '(' . $_right . ')' : $_right)
			)
			->setAjaxEndpoint(
				$this->stepFactory->getAjaxEndpoint(StepPlusController::class, [
					'x' => $left->getNumber()->getHumanString(),
					'y' => $right->getNumber()->getHumanString(),
				])
			);
	}

}

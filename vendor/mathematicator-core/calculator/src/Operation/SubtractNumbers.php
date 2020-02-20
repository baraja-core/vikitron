<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Search\Query;
use Mathematicator\Tokenizer\Token\NumberToken;

class SubtractNumbers
{

	/**
	 * @var NumberFactory
	 */
	private $numberFactory;

	public function __construct(NumberFactory $numberFactory)
	{
		$this->numberFactory = $numberFactory;
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
			$result = bcsub($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $query->getDecimals());
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcsub(
					bcmul($rightFraction[1], $leftFraction[0], $query->getDecimals()),
					bcmul($leftFraction[1], $rightFraction[0], $query->getDecimals()),
					$query->getDecimals()
				)
				. '/'
				. bcmul($leftFraction[1], $rightFraction[1], $query->getDecimals());
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		return (new NumberOperationResult)
			->setNumber($newNumber)
			->setDescription(
				'Odčítání čísel '
				. $left->getNumber()->getHumanString()
				. ' - ' . $right->getNumber()->getHumanString()
			);
	}

}

<?php

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\NumberToken;

class SubtractNumbers
{

	/**
	 * @var NumberFactory
	 */
	private $numberFactory;

	/**
	 * @var int
	 */
	private $tolerance;

	public function __construct(NumberFactory $numberFactory)
	{
		$this->numberFactory = $numberFactory;
		$this->tolerance = 100;
	}

	public function process(NumberToken $left, NumberToken $right)
	{
		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$result = bcsub($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $this->tolerance);
		} else {
			$leftFraction = $left->getNumber()->getFraction();
			$rightFraction = $right->getNumber()->getFraction();

			$result = bcsub(
					bcmul($rightFraction[1], $leftFraction[0], $this->tolerance),
					bcmul($leftFraction[1], $rightFraction[0], $this->tolerance),
					$this->tolerance
				)
				. '/'
				. bcmul($leftFraction[1], $rightFraction[1], $this->tolerance);
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$result = new NumberOperationResult();
		$result->setNumber($newNumber);
		$result->setDescription(
			'Odčítání čísel '
			. $left->getNumber()->getHumanString()
			. ' - ' . $right->getNumber()->getHumanString()
		);

		return $result;
	}

}

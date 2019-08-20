<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;
use Nette\Utils\Validators;

class DivisionNumbers
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
		$leftFraction = $left->getNumber()->getFraction();
		$rightFraction = $right->getNumber()->getFraction();

		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$bcDiv = preg_replace('/\.0+$/', '',
				bcdiv($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $this->tolerance)
			);
			if (Validators::isNumericInt($bcDiv)) {
				$result = $bcDiv;
			} else {
				$result = $left->getNumber()->getInteger() . '/' . $right->getNumber()->getInteger();
			}
		} else {
			$result = bcmul($leftFraction[0], $rightFraction[1], $this->tolerance) . '/' . bcmul($leftFraction[1], $rightFraction[0], $this->tolerance);
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$result = new NumberOperationResult();
		$result->setNumber($newNumber);
		$result->setTitle('Dělení čísel');
		$result->setDescription(
			'Na dělení dvou čísel se můžeme dívat také jako na zlomek. '
			. 'Čísla převedeme na zlomek, který se následně pokusíme zkrátit (pokud to bude možné).'
			. "\n\n"
			. $this->renderDescription($left->getNumber(), $right->getNumber(), $newNumber->getNumber())
			. "\n"
		);

		return $result;
	}

	/**
	 * @param Number $left
	 * @param Number $right
	 * @param Number $result
	 * @return string
	 */
	private function renderDescription(SmartNumber $left, SmartNumber $right, SmartNumber $result): string
	{
		$isEqual = ($left->getHumanString() . '/' . $right->getHumanString()) === $result->getHumanString();

		$fraction = '\frac{' . $left->getString() . '}{' . $right->getString() . '}';

		$return = !$isEqual
			? 'Zlomek \(' . $fraction . '\) lze zkrátit na \(' . $result->getString() . '\).'
			: 'Zlomek \(' . $fraction . '\) je v základním tvaru a nelze dále zkrátit.';

		$return .= "\n\n" . '\(' . $left->getString() . '\ \div\ ' . $right->getString() . '\ =\ '
			. (!$isEqual ? $fraction . '\ =\ ' : '')
			. $result->getString() . '\)' . "\n";

		return $return;
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Search\Query;
use Mathematicator\Tokenizer\Token\NumberToken;
use Nette\Utils\Validators;

class DivisionNumbers
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
		$leftFraction = $left->getNumber()->getFraction();
		$rightFraction = $right->getNumber()->getFraction();

		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			$bcDiv = preg_replace('/\.0+$/', '',
				bcdiv($left->getNumber()->getInteger(), $right->getNumber()->getInteger(), $query->getDecimals())
			);
			if (Validators::isNumericInt($bcDiv)) {
				$result = $bcDiv;
			} else {
				$result = $left->getNumber()->getInteger() . '/' . $right->getNumber()->getInteger();
			}
		} else {
			$result = bcmul($leftFraction[0], $rightFraction[1], $query->getDecimals()) . '/' . bcmul($leftFraction[1], $rightFraction[0], $query->getDecimals());
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		return (new NumberOperationResult)
			->setNumber($newNumber)
			->setTitle('Dělení čísel')
			->setDescription(
				'Na dělení dvou čísel se můžeme dívat také jako na zlomek. '
				. 'Čísla převedeme na zlomek, který se následně pokusíme zkrátit (pokud to bude možné).'
				. "\n\n"
				. $this->renderDescription($left->getNumber(), $right->getNumber(), $newNumber->getNumber())
				. "\n"
			);
	}

	/**
	 * @param SmartNumber $left
	 * @param SmartNumber $right
	 * @param SmartNumber $result
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

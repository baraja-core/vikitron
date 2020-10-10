<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Brick\Math\BigRational;
use Brick\Math\Exception\RoundingNecessaryException;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\Latex\MathLatexBuilder;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;

final class DivisionNumbers
{
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		$leftNumber = $left->getNumber();
		$rightNumber = $right->getNumber();

		if ($leftNumber->isInteger() && $rightNumber->isInteger()) {
			$result = Calculation::of($leftNumber)
				->dividedBy($rightNumber->toBigInteger())
				->getResult()
				->toBigRational()
				->simplified();

			try {
				$result = $result->toBigInteger();
			} catch (RoundingNecessaryException $e) {
			}
		} else {
			$leftFraction = $leftNumber->toBigRational();
			$rightFraction = $rightNumber->toBigRational();

			$result = BigRational::nd(
				$leftFraction->getNumerator()->multipliedBy($rightFraction->getDenominator()),
				$leftFraction->getDenominator()->multipliedBy($rightFraction->getNumerator())
			)->simplified();
		}

		$newNumber = new NumberToken(SmartNumber::of($result));
		$newNumber->setToken((string) $newNumber->getNumber())
			->setPosition($left->getPosition())
			->setType('number');

		return (new NumberOperationResult())
			->setNumber($newNumber)
			->setTitle('Dělení čísel')
			->setDescription(
				'Na dělení dvou čísel se můžeme dívat také jako na zlomek. '
				. 'Čísla převedeme na zlomek, který se následně pokusíme zkrátit (pokud to bude možné).'
				. "\n\n"
				. $this->renderDescription($leftNumber, $rightNumber, $newNumber->getNumber())
				. "\n"
			);
	}


	private function renderDescription(SmartNumber $left, SmartNumber $right, SmartNumber $result): string
	{
		$isEqual = ((string) $left . '/' . (string) $right) === (string) $result;

		$fraction = MathLatexToolkit::frac((string) $left, (string) $right);

		$return = $isEqual
			? 'Zlomek \(' . $fraction . '\) je v základním tvaru a nelze dále zkrátit.'
			: 'Zlomek \(' . $fraction . '\) lze zkrátit na \(' . $result . '\).';

		$returnLatex = (new MathLatexBuilder($left->toLatex()))
			->dividedBy($right->toLatex());

		if (!$isEqual) {
			$returnLatex->equals($fraction);
		}

		$returnLatex->equals($result->toLatex())
			->wrap("\n\n\\(", "\\)\n");

		return $return . $returnLatex;
	}
}

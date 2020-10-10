<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Brick\Math\BigDecimal;
use Brick\Math\BigRational;
use Brick\Math\RoundingMode;
use Mathematicator\Calculator\Step\Controller\StepPowController;
use Mathematicator\Calculator\Step\StepFactory;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Exception\UndefinedOperationException;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;

final class PowNumber
{

	/**
	 * @throws UndefinedOperationException
	 */
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		$leftNumber = $left->getNumber();
		$rightNumber = $right->getNumber();
		$leftFraction = $leftNumber->toBigRational();
		$rightFraction = $rightNumber->toBigRational();

		if (($rightIsInteger = $rightNumber->isInteger()) === true && $leftNumber->isInteger()) {
			if ($leftNumber->isEqualTo(0) && $rightNumber->isEqualTo(0)) {
				throw new UndefinedOperationException(__METHOD__ . ': Undefined operation.');
			}

			$result = Calculation::of($left->getNumber())
				->power($right->getNumber()->toInt())
				->getResult();
		} elseif ($rightIsInteger === true) {
			$result = SmartNumber::of(
				BigRational::nd(
					$leftFraction->getNumerator()->power($right->getNumber()->toInt()),
					$leftFraction->getDenominator()->power($right->getNumber()->toInt())
				)
			);
		} else {
			if ($rightNumber->isNegative() === true) {
				$rightFraction = BigRational::nd(
					$rightFraction->getDenominator(),
					$rightFraction->getNumerator()
				);
			}

			$result = SmartNumber::of(
				BigRational::nd(
					pow(
						$leftFraction->getNumerator()->power($rightFraction->getNumerator()->toInt())->toInt(),
						BigDecimal::one()->dividedBy($rightFraction->getDenominator(), $query->getDecimals(), RoundingMode::HALF_UP)->toFloat()
					),
					pow(
						$leftFraction->getDenominator()->power($rightFraction->getNumerator()->toInt())->toInt(),
						BigDecimal::one()->dividedBy($rightFraction->getDenominator(), $query->getDecimals(), RoundingMode::HALF_UP)->toFloat()
					)
				)
			);
		}

		$newNumber = new NumberToken($result);
		$newNumber->setToken((string) $newNumber->getNumber())
			->setPosition($left->getPosition())
			->setType('number');

		return (new NumberOperationResult())
			->setNumber($newNumber)
			->setTitle('Umocňování čísel ' . $leftNumber->toHumanString() . ' ^ ' . $rightNumber->toHumanString())
			->setDescription($this->renderDescription($leftNumber, $rightNumber, $newNumber->getNumber()))
			->setAjaxEndpoint(
				StepFactory::getAjaxEndpoint(StepPowController::class, [
					'x' => $leftNumber->toHumanString(),
					'y' => $rightNumber->toHumanString(),
					'result' => (string) $newNumber->getNumber(),
				])
			);
	}


	private function renderDescription(SmartNumber $left, SmartNumber $right, SmartNumber $result): string
	{
		if (!$left->isInteger() && !$right->isInteger()) {
			return 'Umocňování zlomků je zatím experimentální a může poskytnout jen přibližný výsledek.';
		}
		if ($right->isEqualTo(0)) {
			return '\({a}^{0}\ =\ 1\) Cokoli na nultou (kromě nuly) je vždy jedna. '
				. 'Umocňování na nultou si lze také představit jako nekonečné odmocňování, '
				. 'proto se limitně blíží k jedné.';
		}

		return (string) MathLatexToolkit::create(
			MathLatexToolkit::pow(
				$left->toHumanString(), $right->toHumanString()
			)->equals((string) $result),
			'\(', '\)'
		);
	}
}

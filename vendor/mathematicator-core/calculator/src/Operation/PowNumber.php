<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\Query;
use Mathematicator\Engine\UndefinedOperationException;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Step\Controller\StepPowController;
use Mathematicator\Step\StepFactory;
use Mathematicator\Tokenizer\Token\NumberToken;

class PowNumber
{

	/** @var NumberFactory */
	private $numberFactory;

	/** @var StepFactory */
	private $stepFactory;


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
	 * @throws UndefinedOperationException
	 */
	public function process(NumberToken $left, NumberToken $right, Query $query): NumberOperationResult
	{
		$leftFraction = $left->getNumber()->getFraction();
		$rightFraction = $right->getNumber()->getFraction();

		$result = null;

		if (($rightInteger = $right->getNumber()->isInteger()) && $left->getNumber()->isInteger()) {
			if ($left->getNumber()->getInteger() === '0' && $right->getNumber()->getInteger() === '0') {
				throw new UndefinedOperationException(__METHOD__ . ': Undefined operation.');
			}

			$result = bcpow($left->getToken(), $right->getToken(), $query->getDecimals());
		} elseif ($rightInteger === true) {
			$result = bcpow($leftFraction[0], $right->getToken(), $query->getDecimals()) . '/' . bcpow($leftFraction[1], $right->getToken(), $query->getDecimals());
		} else {
			if ($right->getNumber()->isNegative()) {
				$rightFraction = [
					$rightFraction[1],
					$rightFraction[0],
				];
			}

			$result = pow(
					(float) bcpow($leftFraction[0], $rightFraction[0], $query->getDecimals()),
					(float) bcdiv('1', $rightFraction[1], $query->getDecimals())
				)
				. '/'
				. pow(
					(float) bcpow($leftFraction[1], $rightFraction[0], $query->getDecimals()),
					(float) bcdiv('1', $rightFraction[1], $query->getDecimals())
				);
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		return (new NumberOperationResult)
			->setNumber($newNumber)
			->setTitle('Umocňování čísel ' . $left->getNumber()->getHumanString() . ' ^ ' . $right->getNumber()->getHumanString())
			->setDescription($this->renderDescription($left->getNumber(), $right->getNumber(), $newNumber->getNumber()))
			->setAjaxEndpoint(
				$this->stepFactory->getAjaxEndpoint(StepPowController::class, [
					'x' => $left->getNumber()->getHumanString(),
					'y' => $right->getNumber()->getHumanString(),
					'result' => $newNumber->getNumber()->getString(),
				])
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
		if (!$left->isInteger() && !$right->isInteger()) {
			return 'Umocňování zlomků je zatím experimentální a může poskytnout jen přibližný výsledek.';
		}

		if ($right->isInteger() && $right->getInteger() === '0') {
			return '\({a}^{0}\ =\ 1\) Cokoli na nultou (kromě nuly) je vždy jedna. '
				. 'Umocňování na nultou si lze také představit jako nekonečné odmocňování, '
				. 'proto se limitně blíží k jedné.';
		}

		return '\({' . $left->getHumanString() . '}^{' . $right->getHumanString() . '}\ =\ ' . $result->getString() . '\)';
	}
}

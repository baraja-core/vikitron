<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Engine\UndefinedOperationException;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\NumberToken;
use Model\Math\Step\Controller\StepPowController;
use Model\Math\Step\StepFactory;

class PowNumber
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

	public function __construct(NumberFactory $numberFactory, StepFactory $stepFactory)
	{
		$this->numberFactory = $numberFactory;
		$this->stepFactory = $stepFactory;
		$this->tolerance = 100;
	}

	public function process(NumberToken $left, NumberToken $right)
	{
		$leftFraction = $left->getNumber()->getFraction();
		$rightFraction = $right->getNumber()->getFraction();

		$result = null;

		if ($left->getNumber()->isInteger() && $right->getNumber()->isInteger()) {
			if ($left->getNumber()->getInteger() === '0' && $right->getNumber()->getInteger() === '0') {
				throw new UndefinedOperationException(__METHOD__ . ': Undefined operation.');
			}

			$result = bcpow($left->getToken(), $right->getToken(), $this->tolerance);
		} elseif ($right->getNumber()->isInteger()) {
			$result = bcpow($leftFraction[0], $right->getToken(), $this->tolerance) . '/' . bcpow($leftFraction[1], $right->getToken(), $this->tolerance);
		} else {
			if ($right->getNumber()->isNegative()) {
				$rightFraction = [
					$rightFraction[1],
					$rightFraction[0],
				];
			}

			$result = pow(bcpow($leftFraction[0], $rightFraction[0], $this->tolerance), bcdiv(1, $rightFraction[1], $this->tolerance))
				. '/'
				. pow(bcpow($leftFraction[1], $rightFraction[0], $this->tolerance), bcdiv(1, $rightFraction[1], $this->tolerance));
		}

		$newNumber = new NumberToken($this->numberFactory->create($result));
		$newNumber->setToken($newNumber->getNumber()->getString());
		$newNumber->setPosition($left->getPosition());
		$newNumber->setType('number');

		$result = new NumberOperationResult();
		$result->setNumber($newNumber);
		$result->setTitle('Umocňování čísel ' . $left->getNumber()->getHumanString() . ' ^ ' . $right->getNumber()->getHumanString());
		$result->setDescription($this->renderDescription($left->getNumber(), $right->getNumber(), $newNumber->getNumber()));
		$result->setAjaxEndpoint(
			$this->stepFactory->getAjaxEndpoint(StepPowController::class, [
				'x' => $left->getNumber()->getHumanString(),
				'y' => $right->getNumber()->getHumanString(),
				'result' => $newNumber->getNumber()->getString(),
			])
		);

		return $result;
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

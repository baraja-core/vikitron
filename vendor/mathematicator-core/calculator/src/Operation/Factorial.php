<?php

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\MathErrorException;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokens;
use Model\Math\Step\StepFactory;

class Factorial
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
		$this->tolerance = 100;
	}

	public function process(FactorialToken $token): NumberOperationResult
	{
		$result = $token->getNumber()->getInteger();
		$number = $this->numberFactory->create($this->bcFact($result));

		$newNumber = new NumberToken($number);
		$newNumber->setToken($number->getHumanString());
		$newNumber->setPosition($token->getPosition());
		$newNumber->setType(Tokens::M_NUMBER);

		$return = new NumberOperationResult;
		$return->setNumber($newNumber);
		$return->setTitle('Faktoriál');
		$return->setDescription(
			'Definice:' . "\n"
			. '\(n!\ =\ n\ \cdot\ (n-1)\ \cdot\ (n-2)\ \cdot\ \cdots\)' . "\n\n"
			. 'Výpočet:' . "\n"
			. $this->getDescriptionTimes($token->getNumber()->getInteger()) . "\n"
			. '\(' . $token->getNumber()->getString() . '!\ =\ '
			. number_format($newNumber->getNumber()->getString(), 0, '', '\ ')
			. '\)'
		);

		return $return;
	}

	private function bcFact(string $num): string
	{
		if ($num === '0') {
			return '1';
		}

		if (!filter_var($num, FILTER_VALIDATE_INT) || $num <= 0) {
			throw new MathErrorException('Argument must be natural number, ' . json_encode($num) . ' given.');
		}

		for ($result = '1'; $num > 0; $num--) {
			$result = bcmul($result, $num);
		}

		return $result;
	}

	/**
	 * @param int $n
	 * @return string
	 */
	private function getDescriptionTimes(int $n): string
	{
		if ($n === 0) {
			return 'Definice: Faktoriál nuly je jedna.';
		}

		$return = '';

		if ($n < 10) {
			for ($i = $n; $i >= 1; $i--) {
				$return .= ($return ? '\ \cdot\ ' : '') . $i;
			}
		} else {
			for ($i = $n; $i >= 1; $i--) {
				$return .= ($return ? '\ \cdot\ ' : '') . $i;
				if ($n - $i + 1 >= 3) {
					break;
				}
			}

			$return .= '\ \cdot\ \cdots';

			for ($i = 3; $i >= 1; $i--) {
				$return .= ($return ? '\ \cdot\ ' : '') . $i;
			}
		}

		return '\(' . $n . '!\ =\ ' . $return . '\)';
	}

}
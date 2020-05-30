<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\MathErrorException;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokens;

final class Factorial
{

	/** @var NumberFactory */
	private $numberFactory;


	/**
	 * @param NumberFactory $numberFactory
	 */
	public function __construct(NumberFactory $numberFactory)
	{
		$this->numberFactory = $numberFactory;
	}


	/**
	 * @param FactorialToken $token
	 * @return NumberOperationResult
	 * @throws MathErrorException
	 */
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
		$return->setTitle('Faktoriál ' . $result . '!');
		$return->setDescription(
			'Definice:' . "\n"
			. '\(n!\ =\ n\ \cdot\ (n-1)\ \cdot\ (n-2)\ \cdot\ \cdots\)' . "\n\n"
			. 'Výpočet:' . "\n"
			. $this->getDescriptionTimes((int) $token->getNumber()->getInteger()) . "\n"
			. '\(' . $token->getNumber()->getString() . '!\ =\ '
			. preg_replace('/(\d{3})/', '$1\ ', $newNumber->getNumber()->getString())
			. '\)'
		);

		return $return;
	}


	/**
	 * @param string $num
	 * @return string
	 * @throws MathErrorException
	 */
	private function bcFact(string $num): string
	{
		if ($num === '0') {
			return '1';
		}

		if ($num <= 0 || !filter_var($num, FILTER_VALIDATE_INT)) {
			throw new MathErrorException('Argument must be natural number, "' . $num . '" given.');
		}

		for ($result = 1; $num > 0; $num--) {
			$result = bcmul((string) $result, (string) (int) $num);
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
			$return .= '\ \cdot\ \cdots';

			for ($i = 3; $i >= 1; $i--) {
				$return .= ($return ? '\ \cdot\ ' : '') . $i;
			}
		}

		return '\(' . $n . '!\ =\ ' . $return . '\)';
	}
}

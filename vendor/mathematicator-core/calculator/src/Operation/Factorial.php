<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Brick\Math\BigInteger;
use Mathematicator\Engine\Exception\MathErrorException;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokens;

final class Factorial
{

	/**
	 * @throws MathErrorException
	 */
	public function process(FactorialToken $token): NumberOperationResult
	{
		$result = $token->getNumber()->toBigInteger();
		$number = SmartNumber::of($this->bcFact($result));

		$newNumber = new NumberToken($number);
		$newNumber->setToken((string) $number->toHumanString())
			->setPosition($token->getPosition())
			->setType(Tokens::M_NUMBER);

		$return = new NumberOperationResult();
		$return->setNumber($newNumber);
		$return->setTitle('Faktoriál ' . $result . '!');
		$return->setDescription(
			'Definice:' . "\n"
			. '\(n!\ =\ n\ \cdot\ (n-1)\ \cdot\ (n-2)\ \cdot\ \cdots\)' . "\n\n"
			. 'Výpočet:' . "\n"
			. $this->getDescriptionTimes($token->getNumber()->toInt()) . "\n"
			. '\(' . $token->getNumber() . '!\ =\ '
			. preg_replace('/(\d{3})/', '$1\ ', (string) $newNumber->getNumber())
			. '\)'
		);

		return $return;
	}


	/**
	 * @throws MathErrorException
	 */
	private function bcFact(BigInteger $num): BigInteger
	{
		if ($num->isEqualTo(0)) {
			return BigInteger::one();
		}
		if ($num->isLessThanOrEqualTo(0)) {
			throw new MathErrorException('Argument must be natural number, "' . $num . '" given.');
		}
		for ($result = BigInteger::one(); $num->isGreaterThan(0); $num = $num->minus(1)) {
			$result = $result->multipliedBy($num);
		}

		return $result;
	}


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

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\MathFunction\Functions;


use Mathematicator\Calculator\MathFunction\FunctionResult;
use Mathematicator\Calculator\MathFunction\IFunction;
use Mathematicator\Numbers\SmartNumber;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;

final class AbsFunction implements IFunction
{

	/**
	 * @param NumberToken|IToken $token
	 * @return FunctionResult
	 */
	public function process(IToken $token): FunctionResult
	{
		if (!($token instanceof NumberToken)) {
			throw new \InvalidArgumentException();
		}

		$result = new FunctionResult();

		$abs = $token->getNumber()->getNumber()->toBigDecimal()->abs();
		$token->setNumber(SmartNumber::of($abs));

		$result->setOutput($token);

		return $result;
	}


	public function isValidInput(IToken $token): bool
	{
		return $token instanceof NumberToken;
	}
}

<?php

namespace Model\Math\MathFunction;


use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;

class AbsFunction implements IFunction
{

	/**
	 * @param NumberToken|IToken $token
	 * @return FunctionResult
	 */
	public function process(IToken $token): FunctionResult
	{
		$result = new FunctionResult();

		$abs = preg_replace('/^-/', '', $token->getNumber()->getInput());
		$token->getNumber()->setValue($abs);

		$result->setOutput($token);

		return $result;
	}

	/**
	 * @param IToken $token
	 * @return bool
	 */
	public function isValidInput(IToken $token): bool
	{
		return $token instanceof NumberToken;
	}

}

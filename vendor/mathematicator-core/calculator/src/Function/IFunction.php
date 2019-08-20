<?php

namespace Model\Math\MathFunction;


use Mathematicator\Tokenizer\Token\IToken;

interface IFunction
{

	/**
	 * @param IToken $token
	 * @return FunctionResult
	 */
	public function process(IToken $token): FunctionResult;

	/**
	 * @param IToken $token
	 * @return bool
	 */
	public function isValidInput(IToken $token): bool;

}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\MathFunction;


use Mathematicator\Tokenizer\Token\IToken;

interface IFunction
{
	public function process(IToken $token): FunctionResult;

	public function isValidInput(IToken $token): bool;
}

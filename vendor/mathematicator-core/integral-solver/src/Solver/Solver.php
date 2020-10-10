<?php

declare(strict_types=1);

namespace Mathematicator\Integral\Solver;


use Mathematicator\Engine\Exception\MathematicatorException;
use Mathematicator\Integral\Exception\CanNotSolveException;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Token\VariableToken;

class Solver
{

	/**
	 * @param IToken[] $tokens
	 * @return string
	 * @throws CanNotSolveException|MathematicatorException
	 */
	public function solvePart(array $tokens): string
	{
		if (\count($tokens) === 1) {
			return $this->solvePrimitiveIntegral($tokens[0]);
		}

		CanNotSolveException::canNotSolve($tokens);

		return '';
	}


	/**
	 * @throws CanNotSolveException
	 */
	private function solvePrimitiveIntegral(IToken $token): string
	{
		if ($token instanceof NumberToken) {
			if ($token->getToken() !== '0') {
				return ($num = (string) $token->getNumber()) === '1' ? 'x' : $num . 'x';
			}

			return '';
		}
		if ($token instanceof VariableToken) {
			if (($times = (string) $token->getTimes()) === '1') {
				return '(x^2)/2';
			}

			CanNotSolveException::notImplemented('Variable ' . $times . 'x');
		}

		CanNotSolveException::notImplemented($token->getType() . ', ' . $token->getToken());

		return '?';
	}
}

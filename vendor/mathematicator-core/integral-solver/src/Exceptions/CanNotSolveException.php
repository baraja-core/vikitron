<?php

declare(strict_types=1);

namespace Mathematicator\Integral\Exception;


use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\SubToken;

class CanNotSolveException extends MathematicatorException
{

	/**
	 * @param IToken[] $tokens
	 * @param int $level
	 * @throws CanNotSolveException
	 */
	public static function canNotSolve(array $tokens, int $level = 0): void
	{
		static $buffer;
		if ($level === 0) {
			$buffer = '';
		}

		foreach ($tokens as $token) {
			$buffer .= $token->getToken();
			if ($token instanceof SubToken) {
				self::canNotSolve($token->getTokens(), $level + 1);
				$buffer .= ')';
			}
		}

		if ($level > 0) {
			return;
		}

		throw new self('Can not solve "' . $buffer . '".');
	}


	/**
	 * @param string $function
	 * @throws CanNotSolveException
	 */
	public static function notImplemented(string $function): void
	{
		throw new self('This function does not implemented "' . $function . '".');
	}
}
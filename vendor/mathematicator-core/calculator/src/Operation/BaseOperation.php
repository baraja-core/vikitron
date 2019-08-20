<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Engine\UndefinedOperationException;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokens;

class BaseOperation
{

	/**
	 * @var AddNumbers
	 */
	private $addNumbers;

	/**
	 * @var SubtractNumbers
	 */
	private $subtractNumbers;

	/**
	 * @var MultiplicationNumber
	 */
	private $multiplicationNumber;

	/**
	 * @var DivisionNumbers
	 */
	private $divisionNumbers;

	/**
	 * @var PowNumber
	 */
	private $powNumbers;

	/**
	 * @var Factorial
	 */
	private $factorial;

	public function __construct(
		AddNumbers $addNumbers,
		SubtractNumbers $subtractNumbers,
		MultiplicationNumber $multiplicationNumber,
		DivisionNumbers $divisionNumbers,
		PowNumber $powNumber,
		Factorial $factorial
	)
	{
		$this->addNumbers = $addNumbers;
		$this->subtractNumbers = $subtractNumbers;
		$this->multiplicationNumber = $multiplicationNumber;
		$this->divisionNumbers = $divisionNumbers;
		$this->powNumbers = $powNumber;
		$this->factorial = $factorial;
	}

	/**
	 * @param NumberToken $left
	 * @param NumberToken $right
	 * @param string $operation
	 * @return NumberOperationResult|null
	 */
	public function process(NumberToken $left, NumberToken $right, string $operation): ?NumberOperationResult
	{
		switch ($operation) {
			case '+':
				return $this->addNumbers->process($left, $right);

			case '-':
				return $this->subtractNumbers->process($left, $right);

			case '*':
				return $this->multiplicationNumber->process($left, $right);

			case '/':
				return $this->divisionNumbers->process($left, $right);

			case '^':
				return $this->powNumbers->process($left, $right);

			default:
				return null;
		}
	}

	/**
	 * @param NumberToken|InfinityToken $left
	 * @param NumberToken|InfinityToken $right
	 * @param string $operation
	 * @return NumberOperationResult|InfinityToken|null
	 * @throws UndefinedOperationException
	 */
	public function processInfinity($left, $right, string $operation)
	{
		$infinity = new InfinityToken();
		$infinity->setToken('INF');
		$infinity->setPosition($left->getPosition());
		$infinity->setType(Tokens::M_INFINITY);
		$result = new NumberOperationResult();

		switch ($operation) {
			case '+':
				return $infinity;

			case '-':
				if ($left instanceof InfinityToken && $right instanceof InfinityToken) {
					throw new UndefinedOperationException('Odčítání nekonečno - nekonečno.');
				}

				if ($left instanceof InfinityToken) {
					return $infinity;
				}

				return $infinity; // TODO: Must return -INF

			case '*':
				return $infinity; // TODO: Check 0 * INF

			case '/':
				if ($left instanceof InfinityToken && $right instanceof InfinityToken) {
					throw new UndefinedOperationException('Odčítání nekonečno / nekonečno.');
				}

				if ($left instanceof InfinityToken) {
					return $infinity;
				}

				throw new UndefinedOperationException('Odčítání číslo / nekonečno.'); // TODO: Verify math correction

			case '^':
				return $infinity; // TODO: Fix me!

			default:
				return null;
		}
	}

	/**
	 * @param FactorialToken $token
	 * @return NumberOperationResult
	 */
	public function processFactorial(FactorialToken $token): NumberOperationResult
	{
		return $this->factorial->process($token);
	}

	/**
	 * @param NumberToken $token
	 * @return NumberOperationResult
	 */
	public function processNumberToFactorial(NumberToken $token): NumberOperationResult
	{
		$factorialToken = new FactorialToken($token->getNumber());
		$factorialToken->setToken($token->getNumber()->getHumanString());
		$factorialToken->setPosition($token->getPosition());
		$factorialToken->setType(Tokens::M_FACTORIAL);

		$return = $this->processFactorial($factorialToken);
		$return->setIteratorStep(1);

		return $return;
	}

}

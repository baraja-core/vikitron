<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Exception\MathematicatorException;
use Mathematicator\Engine\Exception\MathErrorException;
use Mathematicator\Engine\Exception\UndefinedOperationException;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokens;

final class BaseOperation
{

	/** @var AddNumbers */
	private $addNumbers;

	/** @var SubtractNumbers */
	private $subtractNumbers;

	/** @var MultiplicationNumber */
	private $multiplicationNumber;

	/** @var DivisionNumbers */
	private $divisionNumbers;

	/** @var PowNumber */
	private $powNumbers;

	/** @var Factorial */
	private $factorial;


	public function __construct(AddNumbers $addNumbers, SubtractNumbers $subtractNumbers, MultiplicationNumber $multiplicationNumber, DivisionNumbers $divisionNumbers, PowNumber $powNumber, Factorial $factorial)
	{
		$this->addNumbers = $addNumbers;
		$this->subtractNumbers = $subtractNumbers;
		$this->multiplicationNumber = $multiplicationNumber;
		$this->divisionNumbers = $divisionNumbers;
		$this->powNumbers = $powNumber;
		$this->factorial = $factorial;
	}


	/**
	 * @throws MathematicatorException
	 */
	public function process(NumberToken $left, NumberToken $right, string $operation, Query $query): ?NumberOperationResult
	{
		switch ($operation) {
			case '+':
				return $this->addNumbers->process($left, $right, $query);

			case '-':
				return $this->subtractNumbers->process($left, $right, $query);

			case '*':
				return $this->multiplicationNumber->process($left, $right, $query);

			case '/':
				return $this->divisionNumbers->process($left, $right, $query);

			case '^':
				return $this->powNumbers->process($left, $right, $query);

			default:
				return null;
		}
	}


	/**
	 * @param NumberToken|InfinityToken $left
	 * @param NumberToken|InfinityToken $right
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
	 * @param IToken|FactorialToken $token
	 * @return NumberOperationResult
	 * @throws MathErrorException
	 */
	public function processFactorial(IToken $token): NumberOperationResult
	{
		assert($token instanceof FactorialToken);

		return $this->factorial->process($token);
	}


	/**
	 * @throws MathErrorException
	 */
	public function processNumberToFactorial(NumberToken $token): NumberOperationResult
	{
		return $this->processFactorial(
			(new FactorialToken($token->getNumber()))
				->setToken((string) $token->getNumber()->toHumanString())
				->setPosition($token->getPosition())
				->setType(Tokens::M_FACTORIAL)
		)->setIteratorStep(1);
	}
}

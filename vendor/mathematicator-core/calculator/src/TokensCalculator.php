<?php

declare(strict_types=1);

namespace Mathematicator\Calculator;


use Mathematicator\Calculator\Operation\BaseOperation;
use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Engine\UndefinedOperationException;
use Mathematicator\MathFunction\FunctionManager;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Search\Query;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\FunctionToken;
use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Token\OperatorToken;
use Mathematicator\Tokenizer\Token\PolynomialToken;
use Mathematicator\Tokenizer\Token\SubToken;
use Mathematicator\Tokenizer\Token\VariableToken;
use Mathematicator\Tokenizer\TokenIterator;
use Mathematicator\Tokenizer\Tokens;

class TokensCalculator
{

	/**
	 * @var BaseOperation
	 */
	private $baseOperation;

	/**
	 * @var Number
	 */
	private $numberFactory;

	/**
	 * @var FunctionManager
	 */
	private $functionManager;

	/**
	 * @param BaseOperation $baseOperation
	 * @param NumberFactory $numberFactory
	 * @param FunctionManager $functionManager
	 */
	public function __construct(
		BaseOperation $baseOperation,
		NumberFactory $numberFactory,
		FunctionManager $functionManager
	)
	{
		$this->baseOperation = $baseOperation;
		$this->numberFactory = $numberFactory;
		$this->functionManager = $functionManager;
	}

	/**
	 * @param IToken[] $tokens
	 * @param Query $query
	 * @return TokensCalculatorResult
	 * @throws MathematicatorException
	 */
	public function process(array $tokens, Query $query): TokensCalculatorResult
	{
		return $this->iterator($tokens, $query);
	}

	/**
	 * @param IToken[] $tokens
	 * @param Query $query
	 * @param int $ttl
	 * @return TokensCalculatorResult
	 * @throws MathematicatorException
	 */
	private function iterator(array $tokens, Query $query, int $ttl = 1024): TokensCalculatorResult
	{
		if ($ttl <= 0) {
			throw new MathematicatorException('Can not solve, because Calculator is in infinity recursion.');
		}

		$resultEntity = new TokensCalculatorResult;
		$result = [];
		$wasMatched = false;
		$iterator = new TokenIterator($tokens);

		while (true) {
			$token = $iterator->getToken();

			if ($wasMatched === true) {
				$result[] = $token;
			} elseif ($token instanceof NumberToken || $token instanceof VariableToken || $token instanceof InfinityToken) {
				if (($newEntity = $this->solveNumberToken($iterator, $query)) !== null) {
					if ($newEntity instanceof InfinityToken) {
						$result[] = $newEntity;
						$resultEntity->setStepDescription('Operace s nekonečnem');
					} elseif ($newEntity instanceof VariableToken) {
						$result[] = $newEntity;
						$iterator->next(2);
						$resultEntity->setStepDescription('Vynásobení proměnných');
					} elseif ($newEntity instanceof PolynomialToken) {
						$result[] = $newEntity;
						$iterator->next($newEntity->isAutoPower() ? 2 : 4);
						$resultEntity->setStepTitle('Převod na mnohočlen')
							->setStepDescription('Výraz: \(' . $newEntity->getToken() . '\)');
					} else {
						$result[] = $newEntity->getNumber();
						$resultEntity->setStepTitle($newEntity->getTitle())
							->setStepDescription($newEntity->getDescription())
							->setAjaxEndpoint($newEntity->getAjaxEndpoint());
						$iterator->next($newEntity->getIteratorStep());
					}
					$wasMatched = true;
				} else {
					$result[] = $token;
					$resultEntity->setStepDescription('Přepsání výrazu');
				}
			} elseif ($token instanceof SubToken) {
				if (\count($token->getTokens()) === 1) {
					if ($token instanceof FunctionToken) {
						$inputToken = $token->getTokens()[0];
						$resultEntity->setStepTitle(
							'Zavolání funkce ' . $token->getName()
							. '(' . ($inputToken instanceof NumberToken
								? $inputToken->getNumber()->getHumanString()
								: $inputToken->getToken()
							) . ')'
						);
						if (($functionResult = $this->functionManager->solve($token->getName(), $inputToken)) === null) {
							$result[] = $inputToken;
						} else {
							$result[] = $functionResult->getOutput();

							if ($functionResult->getStep() !== null) {
								$resultEntity->setStepDescription($functionResult->getStep()->getDescription())
									->setAjaxEndpoint($functionResult->getStep()->getAjaxEndpoint());
							}
						}
					} else {
						$resultEntity->setStepTitle('Odstranění závorky');
						$result[] = $token->getTokens()[0];
					}
				} else {
					$_result = $this->iterator($token->getTokens(), $query, $ttl - 1);
					$_resultResult = $_result->getResult();
					$resultEntity->setStepTitle($_result->getStepTitle())
						->setStepDescription($_result->getStepDescription())
						->setAjaxEndpoint($_result->getAjaxEndpoint());
					$token->setObjectTokens(\count($_resultResult) === 1
						? [$_resultResult[0]]
						: $_resultResult);
					$result[] = $token;
				}
				$wasMatched = true;
			} elseif ($token instanceof FactorialToken) {
				$newEntity = $this->baseOperation->processFactorial($token);
				$result[] = $newEntity->getNumber();
				$resultEntity->setStepTitle($newEntity->getTitle())
					->setStepDescription($newEntity->getDescription())
					->setAjaxEndpoint($newEntity->getAjaxEndpoint());
			} elseif ($token instanceof OperatorToken && \count($result) === 0 && $iterator->getNextToken() instanceof NumberToken) {
				$result[] = (new NumberToken($this->numberFactory->create('')))
					->setPosition($token->getPosition())
					->setToken('0')
					->setType(Tokens::M_NUMBER);
				$result[] = $token;
				$wasMatched = true;
			} else {
				$result[] = $token;
			}

			if ($wasMatched === false) {
				$this->orderByType($iterator);
			}

			$iterator->next();
			if ($iterator->isFinal()) {
				break;
			}
		}

		return $resultEntity->setResult($result);
	}

	/**
	 * @param TokenIterator $iterator
	 * @param Query $query
	 * @return IToken|Operation\NumberOperationResult|InfinityToken|VariableToken|null
	 * @throws UndefinedOperationException|MathematicatorException
	 */
	private function solveNumberToken(TokenIterator $iterator, Query $query)
	{
		$leftNumber = $iterator->getToken();
		$rightNumber = $iterator->getNextToken(2);
		$operator = $iterator->getNextToken();
		$nextOperator = $iterator->getNextToken(3);

		// 1. Polynomial in format `a * x^b`
		if ($leftNumber instanceof NumberToken
			&& $rightNumber instanceof VariableToken
			&& $operator instanceof OperatorToken && $operator->getToken() === '*'
			&& !$iterator->getNextToken(4) instanceof SubToken
		) {
			$powerToken = $iterator->getNextToken(4);
			if ($powerToken instanceof NumberToken
				&& $nextOperator instanceof OperatorToken && $nextOperator->getToken() === '^'
			) { // Format a * x^b
				return new PolynomialToken($leftNumber, $powerToken, $rightNumber);
			}

			// Format a * x^1
			return new PolynomialToken($leftNumber, null, $rightNumber);
		}

		// 2. Variable times number without power in format `x * n` or `n * x`
		if ((($leftNumber instanceof VariableToken && $rightNumber instanceof NumberToken)
				|| ($leftNumber instanceof NumberToken && $rightNumber instanceof VariableToken)
			) && $operator instanceof OperatorToken && $operator->getToken() === '*'
			&& ($nextOperator instanceof OperatorToken && $nextOperator->getToken() === '^') === false
		) {
			$variable = $leftNumber instanceof VariableToken ? $leftNumber : $rightNumber;
			$number = $leftNumber instanceof NumberToken ? $leftNumber : $rightNumber;

			if ($variable !== null && $number !== null) {
				if (($newVariable = $this->baseOperation->process(new NumberToken($variable->getTimes()), $number, '*', $query)) === null) {
					return null;
				}

				return (new VariableToken(
					$variable->getToken(),
					$newVariable->getNumber()->getNumber()
				))->setPosition($variable->getPosition());
			}
		}

		// 3. Variable times variable in format `x [+-*/] y` for `x === y`
		if ($leftNumber instanceof VariableToken
			&& $rightNumber instanceof VariableToken
			&& $operator instanceof OperatorToken
			&& $leftNumber->getToken() === $rightNumber->getToken()
		) {
			$newVariable = $this->baseOperation->process(
				new NumberToken($leftNumber->getTimes()),
				new NumberToken($rightNumber->getTimes()),
				$operator->getToken(),
				$query
			);

			if ($newVariable === null) {
				return null;
			}

			return (new VariableToken(
				$leftNumber->getToken(),
				$newVariable->getNumber()->getNumber()
			))->setPosition($leftNumber->getPosition());
		}

		// 4. Factorial in format `n!`
		if ($leftNumber instanceof NumberToken && $operator instanceof OperatorToken && $operator->getToken() === '!') {
			return $this->baseOperation->processNumberToFactorial($leftNumber);
		}

		if ($leftNumber !== null && $operator !== null && $rightNumber !== null) {
			if (($leftNumber instanceof InfinityToken || $rightNumber instanceof InfinityToken)
				&& ($tryInfinity = $this->solveInfinityToken($iterator, $iterator->getNextToken())) !== null
			) {
				return $tryInfinity;
			}

			if ($leftNumber instanceof NumberToken
				&& $operator instanceof OperatorToken
				&& $rightNumber instanceof NumberToken
				&& ($nextOperator === null
					|| ($nextOperator instanceof OperatorToken && $nextOperator->getPriority() <= $operator->getPriority())
					|| !$nextOperator instanceof OperatorToken
				)
			) {
				return $this->baseOperation->process($leftNumber, $rightNumber, $operator->getToken(), $query);
			}
		}

		return null;
	}

	/**
	 * @param TokenIterator $iterator
	 * @param IToken|OperatorToken|null $operator
	 * @return Operation\NumberOperationResult|InfinityToken|null
	 * @throws UndefinedOperationException
	 */
	private function solveInfinityToken(TokenIterator $iterator, ?IToken $operator)
	{
		if (($leftNumber = $iterator->getToken()) !== null && ($rightNumber = $iterator->getNextToken(2)) !== null
			&& ($leftNumber instanceof InfinityToken || $rightNumber instanceof InfinityToken)
			&& ($leftNumber instanceof NumberToken || $leftNumber instanceof InfinityToken)
			&& ($rightNumber instanceof NumberToken || $rightNumber instanceof InfinityToken)
			&& $operator instanceof OperatorToken
			&& (($nextOperator = $iterator->getNextToken(3)) === null
				|| ($nextOperator instanceof OperatorToken && $nextOperator->getPriority() <= $operator->getPriority())
				|| !$nextOperator instanceof OperatorToken
			)
		) {
			return $this->baseOperation->processInfinity($leftNumber, $rightNumber, $operator->getToken());
		}

		return null;
	}

	/**
	 * @param TokenIterator $iterator
	 * @return TokenIterator
	 */
	private function orderByType(TokenIterator $iterator): TokenIterator
	{
		return $iterator;
	}

}

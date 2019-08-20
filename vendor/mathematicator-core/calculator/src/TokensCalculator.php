<?php

namespace Mathematicator\Calculator;


use Mathematicator\Calculator\Operation\BaseOperation;
use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Numbers\NumberFactory;
use Mathematicator\Tokenizer\Token\FactorialToken;
use Mathematicator\Tokenizer\Token\FunctionToken;
use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Token\OperatorToken;
use Mathematicator\Tokenizer\Token\SubToken;
use Mathematicator\Tokenizer\Token\VariableToken;
use Mathematicator\Tokenizer\TokenIterator;
use Mathematicator\Tokenizer\Tokens;
use Model\Math\MathFunction\FunctionManager;

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
	 * @param array $tokens
	 * @return TokensCalculatorResult
	 * @throws MathematicatorException
	 */
	public function process(array $tokens): TokensCalculatorResult
	{
		return $this->iterator($tokens);
	}

	/**
	 * @param IToken[] $tokens
	 * @param int $ttl
	 * @return TokensCalculatorResult
	 * @throws MathematicatorException
	 */
	private function iterator(array $tokens, int $ttl = 1024): TokensCalculatorResult
	{
		if ($ttl <= 0) {
			throw new MathematicatorException('Can not solve, because Calculator is in infinity recursion.');
		}

		$resultEntity = new TokensCalculatorResult();
		$result = [];
		$wasMatched = false;
		$iterator = new TokenIterator($tokens);

		while (true) {
			$token = $iterator->getToken();

			if ($wasMatched === true) {
				$result[] = $token;
			} else {
				if ($token instanceof NumberToken || $token instanceof VariableToken || $token instanceof InfinityToken) {
					$newEntity = $this->solveNumberToken($iterator);
					if ($newEntity !== null) {
						if ($newEntity instanceof InfinityToken) {
							$result[] = $newEntity;
							$resultEntity->setStepDescription('Operace s nekonečnem');
						} elseif ($newEntity instanceof VariableToken) {
							$result[] = $newEntity;
							$iterator->next(2);
							$resultEntity->setStepDescription('Vynásobení proměnných');
						} else {
							$result[] = $newEntity->getNumber();
							$resultEntity->setStepTitle($newEntity->getTitle());
							$resultEntity->setStepDescription($newEntity->getDescription());
							$resultEntity->setAjaxEndpoint($newEntity->getAjaxEndpoint());
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
							$functionResult = $this->functionManager->solve(
								$token->getName(),
								$inputToken
							);
							if ($functionResult === null) {
								$result[] = $inputToken;
							} else {
								$result[] = $functionResult->getOutput();

								if ($functionResult->getStep() !== null) {
									$resultEntity->setStepDescription($functionResult->getStep()->getDescription());
									$resultEntity->setAjaxEndpoint($functionResult->getStep()->getAjaxEndpoint());
								}
							}
						} else {
							$resultEntity->setStepTitle('Odstranění závorky');
							$result[] = $token->getTokens()[0];
						}
					} else {
						$_result = $this->iterator($token->getTokens(), $ttl - 1);
						$_resultResult = $_result->getResult();
						$resultEntity->setStepTitle($_result->getStepTitle());
						$resultEntity->setStepDescription($_result->getStepDescription());
						$resultEntity->setAjaxEndpoint($_result->getAjaxEndpoint());
						$token->setObjectTokens(\count($_resultResult) === 1
							? [$_resultResult[0]]
							: $_resultResult);
						$result[] = $token;
					}
					$wasMatched = true;
				} elseif ($token instanceof FactorialToken) {
					$newEntity = $this->baseOperation->processFactorial($token);
					$result[] = $newEntity->getNumber();
					$resultEntity->setStepTitle($newEntity->getTitle());
					$resultEntity->setStepDescription($newEntity->getDescription());
					$resultEntity->setAjaxEndpoint($newEntity->getAjaxEndpoint());
				} elseif ($token instanceof OperatorToken && \count($result) === 0 && $iterator->getNextToken() instanceof NumberToken) {
					$zeroToken = new NumberToken($this->numberFactory->create(''));
					$zeroToken->setPosition($token->getPosition())
						->setToken(0)
						->setType(Tokens::M_NUMBER);
					$result[] = $zeroToken;
					$result[] = $token;
					$wasMatched = true;
				} else {
					$result[] = $token;
				}
			}

			if ($wasMatched === false) {
				$this->orderByType($iterator);
			}

			$iterator->next();
			if ($iterator->isFinal()) {
				break;
			}
		}

		$resultEntity->setResult($result);

		return $resultEntity;
	}

	/**
	 * @param TokenIterator $iterator
	 * @return Operation\NumberOperationResult|InfinityToken|VariableToken|null
	 */
	private function solveNumberToken(TokenIterator $iterator)
	{
		$leftNumber = $iterator->getToken();
		$rightNumber = $iterator->getNextToken(2);
		$operator = $iterator->getNextToken();
		$nextOperator = $iterator->getNextToken(3);

		if ((($leftNumber instanceof VariableToken && $rightNumber instanceof NumberToken)
				|| ($leftNumber instanceof NumberToken && $rightNumber instanceof VariableToken)
			) && $operator instanceof OperatorToken && $operator->getToken() === '*'
		) {
			$variable = $leftNumber instanceof VariableToken ? $leftNumber : $rightNumber;
			$number = $leftNumber instanceof NumberToken ? $leftNumber : $rightNumber;

			if ($variable !== null && $number !== null) {
				$newVariable = $this->baseOperation->process(new NumberToken($variable->getTimes()), $number, '*');

				if ($newVariable === null) {
					return null;
				}

				$variableResult = new VariableToken(
					$variable->getToken(),
					$newVariable->getNumber()->getNumber()
				);

				$variableResult->setPosition($variable->getPosition());

				return $variableResult;
			}
		}

		if ($leftNumber instanceof VariableToken
			&& $rightNumber instanceof VariableToken
			&& $operator instanceof OperatorToken
			&& $leftNumber->getToken() === $rightNumber->getToken()
		) {
			$newVariable = $this->baseOperation->process(
				new NumberToken($leftNumber->getTimes()),
				new NumberToken($rightNumber->getTimes()),
				$operator->getToken()
			);

			if ($newVariable === null) {
				return null;
			}

			$variableResult = new VariableToken(
				$leftNumber->getToken(),
				$newVariable->getNumber()->getNumber()
			);

			$variableResult->setPosition($leftNumber->getPosition());

			return $variableResult;
		}

		if ($operator !== null
			&& $leftNumber instanceof NumberToken
			&& $operator instanceof OperatorToken
			&& $operator->getToken() === '!'
		) {
			return $this->baseOperation->processNumberToFactorial($leftNumber);
		}

		if ($leftNumber !== null && $operator !== null && $rightNumber !== null) {
			if ($leftNumber instanceof InfinityToken || $rightNumber instanceof InfinityToken) {
				$tryInfinity = $this->solveInfinityToken($iterator);

				if ($tryInfinity !== null) {
					return $tryInfinity;
				}
			}

			if ($leftNumber instanceof NumberToken
				&& $operator instanceof OperatorToken
				&& $rightNumber instanceof NumberToken
				&& ($nextOperator === null
					|| ($nextOperator instanceof OperatorToken && $nextOperator->getPriority() <= $operator->getPriority())
					|| !$nextOperator instanceof OperatorToken
				)
			) {
				return $this->baseOperation->process($leftNumber, $rightNumber, $operator->getToken());
			}
		}

		return null;
	}

	/**
	 * @param TokenIterator $iterator
	 * @return Operation\NumberOperationResult|InfinityToken|null
	 */
	private function solveInfinityToken(TokenIterator $iterator)
	{
		$leftNumber = $iterator->getToken();
		$rightNumber = $iterator->getNextToken(2);
		$operator = $iterator->getNextToken();
		$nextOperator = $iterator->getNextToken(3);

		if ($leftNumber !== null && $rightNumber !== null
			&& ($leftNumber instanceof InfinityToken || $rightNumber instanceof InfinityToken)
			&& ($leftNumber instanceof NumberToken || $leftNumber instanceof InfinityToken)
			&& $operator instanceof OperatorToken
			&& ($rightNumber instanceof NumberToken || $rightNumber instanceof InfinityToken)
			&& ($nextOperator === null
				|| ($nextOperator instanceof OperatorToken && $nextOperator->getPriority() <= $operator->getPriority())
				|| !$nextOperator instanceof OperatorToken
			)
		) {
			return $this->baseOperation->processInfinity($leftNumber, $rightNumber, $operator->getToken());
		}

		return null;
	}

	private function orderByType(TokenIterator $iterator): TokenIterator
	{
		bdump($iterator);

		return $iterator;
	}

}

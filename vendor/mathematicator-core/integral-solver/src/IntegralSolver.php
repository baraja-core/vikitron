<?php

declare(strict_types=1);

namespace Mathematicator\Integral;


use Mathematicator\Calculator\Step;
use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Engine\QueryNormalizer;
use Mathematicator\Integral\Exception\CanNotSolveException;
use Mathematicator\Integral\Rule\Rule;
use Mathematicator\Integral\Solver\Solver;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\OperatorToken;
use Mathematicator\Tokenizer\Token\SubToken;
use Mathematicator\Tokenizer\Token\VariableToken;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Tokenizer\Exception;

/**
 * This class is user interface for call inner integral logic.
 *
 * Solve all your integrals simply:
 *
 * ->process('1 + x') -> 'x + (x^2)/2 + c'
 */
class IntegralSolver
{

	/** @var Rule[] */
	public $rules = [];

	/** @var Tokenizer */
	private $tokenizer;

	/** @var QueryNormalizer */
	private $queryNormalizer;

	/** @var Solver */
	private $solver;


	/**
	 * @param Tokenizer $tokenizer
	 * @param QueryNormalizer $queryNormalizer
	 * @param Solver $solver
	 */
	public function __construct(Tokenizer $tokenizer, QueryNormalizer $queryNormalizer, Solver $solver)
	{
		$this->tokenizer = $tokenizer;
		$this->queryNormalizer = $queryNormalizer;
		$this->solver = $solver;
	}


	/**
	 * @param string $query
	 * @param string|null $differential
	 * @return IntegralResult
	 * @throws MathematicatorException|Exception
	 */
	public function process(string $query, ?string $differential = null): IntegralResult
	{
		$tokens = $this->tokenizer->tokensToObject(
			$this->tokenizer->tokenize(
				$this->queryNormalizer->normalize($query)
			)
		);

		return $this->processByTokens($tokens, $differential);
	}


	/**
	 * @param IToken[] $tokens
	 * @param string|null $differential
	 * @return IntegralResult
	 * @throws MathematicatorException
	 */
	public function processByTokens(array $tokens, ?string $differential = null): IntegralResult
	{
		$explode = $this->explodeToParts($tokens);
		$differential = $this->resolveDifferential($tokens, $differential);

		$result = new IntegralResult(
			$tokens,
			$this->tokenizer->tokensToLatex($tokens),
			$differential,
			\count($explode['parts']) === 1
		);

		$result->addStep(new Step('Vezměte integrál', $result->getQueryLaTeX()));

		$stepParts = '';
		$partSolutions = [];
		for ($i = 0; isset($explode['parts'][$i]) === true; $i++) {
			try {
				$partSolutions[] = $this->solver->solvePart($explode['parts'][$i]) . $explode['partOperators'][$i];
			} catch (CanNotSolveException $e) {
				$partSolutions[] = '?';
			}

			$partLaTeX = $this->tokenizer->tokensToLatex($explode['parts'][$i]);
			$stepParts .= '\int ' . (\count($explode['parts'][$i]) === 1 ? $partLaTeX : '\left(' . $partLaTeX . '\right)')
				. '\ d' . $differential . $explode['partOperators'][$i];
		}

		if ($result->isSingleToken() === false) {
			$result->addStep(new Step(
				'Rozdělte integrál na části',
				'= ' . $stepParts,
				'Základní věta algebry říká, že lze součet integrálů rozdělit na skupinu menších problémů a ty řešit samostatně.'
			));
		}

		$result->setResult(implode('', $partSolutions) . ($partSolutions !== [] ? '+c' : 'c'));

		try {
			$resultTokens = $this->tokenizer->tokenize($result->getResult());
			$resultObjects = $this->tokenizer->tokensToObject($resultTokens);
			$resultLaTeX = $this->tokenizer->tokensToLatex($resultObjects);

			if ($result->isFinalResult() === true) {
				$result->addStep(new Step('Nalezení primitivní funkce', $resultLaTeX));
			} else {
				$result->addStep(new Step('Přibližné řešení', $resultLaTeX, 'Nepodařilo se najít primitivní funkci, řešení je zobrazeno přibližně.'));
			}
		} catch (Exception $e) {
			$result->addStep(new Step('Řešení', null, 'Řešení se nepodařilo vykreslit.'));
		}

		return $result;
	}


	/**
	 * @param Rule $rule
	 * @return IntegralSolver
	 */
	public function addRule(Rule $rule): self
	{
		$this->rules[] = $rule;

		return $this;
	}


	/**
	 * @param IToken[] $tokens
	 * @return mixed[]
	 */
	private function explodeToParts(array $tokens): array
	{
		$parts = [];
		$partOperators = [];
		$buffer = [];
		$buffering = false;

		foreach ($tokens as $token) {
			if ($buffer === [] || $buffering === true) { // Start recording
				$buffering = true;
			}

			if ($token instanceof OperatorToken && \in_array($token->getToken(), ['+', '-'], true) === true) {
				$parts[] = $buffer;
				$partOperators[] = $token->getToken();
				$buffer = [];
				$buffering = false;
			} else {
				$buffer[] = $token;
			}
		}

		if ($buffering === true) {
			$parts[] = $buffer;
			$partOperators[] = '';
		}

		return [
			'parts' => $parts,
			'partOperators' => $partOperators,
		];
	}


	/**
	 * @param IToken[] $tokens
	 * @param string|null $preferenceDifferential
	 * @param int $level
	 * @return string
	 */
	private function resolveDifferential(array $tokens, ?string $preferenceDifferential = null, int $level = 0): string
	{
		static $variables = [];
		$variables = $level === 0 ? [] : $variables;
		$preferenceDifferential = $preferenceDifferential ?? 'x';

		foreach ($tokens as $token) {
			if ($token instanceof VariableToken) {
				$variables[$token->getToken()] = true;
			} elseif ($token instanceof SubToken) {
				$this->resolveDifferential($token->getTokens(), $preferenceDifferential, $level + 1);
			}
		}

		if (isset($variables[$preferenceDifferential])) {
			return $preferenceDifferential;
		}

		return $variables === [] ? 'x' : array_keys($variables)[0];
	}
}
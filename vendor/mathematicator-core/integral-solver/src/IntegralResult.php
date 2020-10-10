<?php

declare(strict_types=1);

namespace Mathematicator\Integral;


use Mathematicator\Engine\Step\Step;
use Mathematicator\Tokenizer\Token\IToken;

final class IntegralResult
{

	/** @var IToken[] */
	private $queryTokens;

	/** @var string */
	private $queryLaTeX;

	/** @var string|null */
	private $result;

	/** @var string */
	private $differential;

	/** @var bool */
	private $singleToken;

	/** @var Step[] */
	private $steps = [];


	/**
	 * @param IToken[] $queryTokens
	 * @param string $queryLaTeX
	 * @param string $differential
	 * @param bool $singleToken
	 */
	public function __construct(array $queryTokens, string $queryLaTeX, string $differential, bool $singleToken)
	{
		$this->queryTokens = $queryTokens;
		$this->queryLaTeX = $queryLaTeX;
		$this->differential = $differential;
		$this->singleToken = $singleToken;
	}


	/**
	 * @return bool
	 */
	public function isFinalResult(): bool
	{
		return strpos($this->getResult(), '?') === false;
	}


	/**
	 * @return string
	 */
	public function getResult(): string
	{
		return $this->result ?? '';
	}


	/**
	 * @internal
	 * @param string $result
	 */
	public function setResult(string $result): void
	{
		$this->result = $result;
	}


	/**
	 * @return string
	 */
	public function getDifferential(): string
	{
		return $this->differential;
	}


	/**
	 * @return bool
	 */
	public function isSingleToken(): bool
	{
		return $this->singleToken;
	}


	/**
	 * @return IToken[]
	 */
	public function getQueryTokens(): array
	{
		return $this->queryTokens;
	}


	/**
	 * @return string
	 */
	public function getQueryLaTeX(): string
	{
		return '\int '
			. ($this->isSingleToken() ? $this->queryLaTeX : '\left(' . $this->queryLaTeX . '\right)')
			. ' \ d' . $this->getDifferential();
	}


	/**
	 * @return Step[]
	 */
	public function getSteps(): array
	{
		return $this->steps;
	}


	/**
	 * @param Step $step
	 * @return IntegralResult
	 */
	public function addStep(Step $step): self
	{
		$this->steps[] = $step;

		return $this;
	}
}

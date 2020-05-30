<?php

declare(strict_types=1);

namespace Mathematicator\Calculator;


use Mathematicator\Engine\Step;
use Mathematicator\Tokenizer\Token\IToken;
use Nette\SmartObject;

/**
 * @property IToken[] $tokens
 * @property IToken[] $resultTokens
 * @property Step[] $steps
 */
class CalculatorResult
{
	use SmartObject;

	/** @var IToken[] */
	private $tokens;

	/** @var IToken[] */
	private $resultTokens = [];

	/** @var Step[] */
	private $steps = [];


	/**
	 * @param IToken[] $tokens
	 */
	public function __construct(array $tokens)
	{
		$this->tokens = $tokens;
	}


	/**
	 * @return string
	 */
	public function __toString(): string
	{
		if (isset($this->resultTokens[0])) {
			return $this->resultTokens[0]->getToken();
		}

		return '';
	}


	/**
	 * @return IToken[]
	 */
	public function getTokens(): array
	{
		return $this->tokens;
	}


	/**
	 * @param IToken[] $tokens
	 */
	public function setTokens(array $tokens): void
	{
		$this->tokens = $tokens;
	}


	/**
	 * @return IToken[]
	 */
	public function getResultTokens(): array
	{
		return $this->resultTokens;
	}


	/**
	 * @param IToken[] $resultTokens
	 */
	public function setResultTokens(array $resultTokens): void
	{
		$this->resultTokens = $resultTokens;
	}


	/**
	 * @return Step[]
	 */
	public function getSteps(): array
	{
		return $this->steps;
	}


	/**
	 * @param Step[] $steps
	 */
	public function setSteps(array $steps): void
	{
		$return = [];

		$lastStepHash = null;
		foreach ($steps as $step) {
			$stepHash = $step->getTitle()
				. '_' . md5((string) $step->getDescription())
				. '_' . md5(trim((string) $step->getLatex()))
				. '_' . $step->getAjaxEndpoint();

			if ($stepHash !== $lastStepHash) { // Ignore internal same steps
				$return[] = $step;
			}

			$lastStepHash = $stepHash;
		}

		$this->steps = $return;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\MathFunction;


use Mathematicator\Engine\Step;
use Mathematicator\Tokenizer\Token\IToken;
use Nette\SmartObject;

/**
 * @property IToken|IToken[] $input
 * @property IToken|IToken[] $output
 * @property Step|null $step
 */
class FunctionResult
{
	use SmartObject;

	/** @var IToken|IToken[] */
	private $input;

	/** @var IToken|IToken[] */
	private $output;

	/** @var Step|null */
	private $step;


	/**
	 * @return IToken|IToken[]
	 */
	public function getInput()
	{
		return $this->input;
	}


	/**
	 * @param IToken|IToken[] $input
	 * @return FunctionResult
	 */
	public function setInput($input): self
	{
		$this->input = $input;

		return $this;
	}


	/**
	 * @return IToken|IToken[]
	 */
	public function getOutput()
	{
		return $this->output;
	}


	/**
	 * @param IToken|IToken[] $output
	 * @return FunctionResult
	 */
	public function setOutput($output): self
	{
		$this->output = $output;

		return $this;
	}


	/**
	 * @return Step|null
	 */
	public function getStep(): ?Step
	{
		return $this->step;
	}


	/**
	 * @param Step $steps
	 * @return FunctionResult
	 */
	public function setStep(Step $steps): self
	{
		$this->step = $steps;

		return $this;
	}
}

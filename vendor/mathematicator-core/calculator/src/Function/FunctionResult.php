<?php

namespace Model\Math\MathFunction;

use Mathematicator\Calculator\Step;
use Mathematicator\Tokenizer\Token\IToken;
use Nette\SmartObject;

/**
 * @property IToken|IToken[] $input
 * @property IToken|IToken[] $output
 * @property Step $step
 */
class FunctionResult
{

	use SmartObject;

	/**
	 * @var IToken|IToken[]
	 */
	private $input;

	/**
	 * @var IToken|IToken[]
	 */
	private $output;

	/**
	 * @var Step
	 */
	private $step = [];

	/**
	 * @return IToken|IToken[]
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * @param IToken|IToken[] $input
	 */
	public function setInput($input)
	{
		$this->input = $input;
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
	 */
	public function setOutput($output)
	{
		$this->output = $output;
	}

	/**
	 * @return null|Step
	 */
	public function getStep()
	{
		return $this->step;
	}

	/**
	 * @param Step $steps
	 */
	public function setStep(Step $steps)
	{
		$this->step = $steps;
	}

}

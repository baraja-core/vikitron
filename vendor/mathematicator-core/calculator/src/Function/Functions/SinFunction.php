<?php

namespace Model\Math\MathFunction;


use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Token\PiToken;
use Model\Math\Step\Controller\StepSinController;
use Model\Math\Step\StepFactory;

class SinFunction implements IFunction
{


	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @param StepFactory $stepFactory
	 */
	public function __construct(StepFactory $stepFactory)
	{
		$this->stepFactory = $stepFactory;
	}

	/**
	 * @param NumberToken|IToken $token
	 * @return FunctionResult
	 */
	public function process(IToken $token): FunctionResult
	{
		$result = new FunctionResult();

		$x = $token->getNumber()->getFloat();

		if ($token instanceof PiToken) {
			$sin = 0;
		} else {
			$sin = sin($x);
		}

		$token->getNumber()->setValue($sin);
		$token->setToken($sin);

		$step = $this->stepFactory->create();
		$step->setAjaxEndpoint(
			$this->stepFactory->getAjaxEndpoint(StepSinController::class, [
				'x' => $x,
			])
		);

		$result->setStep($step);
		$result->setOutput($token);

		return $result;
	}

	/**
	 * @param IToken $token
	 * @return bool
	 */
	public function isValidInput(IToken $token): bool
	{
		return $token instanceof NumberToken || $token instanceof InfinityToken;
	}

}
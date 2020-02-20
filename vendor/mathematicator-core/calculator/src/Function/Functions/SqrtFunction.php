<?php

declare(strict_types=1);

namespace Mathematicator\MathFunction;


use Mathematicator\Engine\MathErrorException;
use Mathematicator\Step\Controller\StepSqrtController;
use Mathematicator\Step\StepFactory;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;

class SqrtFunction implements IFunction
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
	 * @throws MathErrorException
	 */
	public function process(IToken $token): FunctionResult
	{
		$result = new FunctionResult();

		$n = $token->getNumber()->getFloat();

		if ($n < 0) {
			throw new MathErrorException('Sqrt is smaller than 0, ' . \json_encode($n) . ' given.');
		}

		$sqrt = bcsqrt($n, 100);

		$token->getNumber()->setValue($sqrt);
		$token->setToken($sqrt);

		$step = $this->stepFactory->create();
		$step->setAjaxEndpoint(
			$this->stepFactory->getAjaxEndpoint(StepSqrtController::class, [
				'n' => $n,
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
		return $token instanceof NumberToken;
	}

}

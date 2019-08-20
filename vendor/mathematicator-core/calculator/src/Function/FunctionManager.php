<?php

namespace Model\Math\MathFunction;

use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Tokenizer\Token\IToken;
use Nette\DI\Container;

class FunctionManager
{

	/**
	 * @var string[][]
	 */
	private static $functions = [
		'sqrt' => [
			SqrtFunction::class,
		],
		'abs' => [
			AbsFunction::class,
		],
		'sin' => [
			SinFunction::class,
		],
	];

	/**
	 * @var Container
	 */
	private $serviceFactory;

	/**
	 * @var IFunction
	 */
	private $callback;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->serviceFactory = $container;
	}

	/**
	 * @param string $function
	 * @param IToken $token
	 * @return FunctionResult|null
	 * @throws MathematicatorException
	 */
	public function solve(string $function, IToken $token): ?FunctionResult
	{
		if (!isset(self::$functions[$function])) {
			throw new FunctionDoesNotExistsException('Function [' . $function . '] does not exists.', 500, null, $function);
		}

		foreach (self::$functions[$function] as $callback) {
			$this->callCallback($callback);

			if ($this->callback->isValidInput($token)) {
				return $this->callback->process($token);
			}
		}

		return null; // If token can't solve
	}

	/**
	 * @param string $callback
	 */
	private function callCallback(string $callback): void
	{
		$this->callback = $this->serviceFactory->getByType($callback);
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\MathFunction;


use Mathematicator\Calculator\MathFunction\Functions\AbsFunction;
use Mathematicator\Calculator\MathFunction\Functions\SinFunction;
use Mathematicator\Calculator\MathFunction\Functions\SqrtFunction;
use Mathematicator\Tokenizer\Token\IToken;
use Psr\Container\ContainerInterface;

final class FunctionManager
{

	/** @var string[][] */
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

	/** @var ContainerInterface */
	private $container;

	/** @var IFunction */
	private $callback;


	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}


	/**
	 * @throws FunctionDoesNotExistsException
	 */
	public function solve(string $function, IToken $token): ?FunctionResult
	{
		if (!isset(self::$functions[$function])) {
			throw new FunctionDoesNotExistsException('Function "' . $function . '" does not exist.', 500, null, $function);
		}
		foreach (self::$functions[$function] as $callback) {
			$this->callCallback($callback);
			if ($this->callback->isValidInput($token)) {
				return $this->callback->process($token);
			}
		}

		return null; // If token can't solve
	}


	private function callCallback(string $callback): void
	{
		$this->callback = $this->container->get($callback);
	}
}

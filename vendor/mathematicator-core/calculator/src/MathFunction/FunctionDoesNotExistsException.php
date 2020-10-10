<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\MathFunction;


use Mathematicator\Engine\Exception\MathematicatorException;

final class FunctionDoesNotExistsException extends MathematicatorException
{

	/** @var string */
	private $function;


	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null, string $function = '')
	{
		parent::__construct($message, $code, $previous);
		$this->function = $function;
	}


	public function getFunction(): string
	{
		return $this->function;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\MathFunction;


use Mathematicator\Engine\MathematicatorException;

class FunctionDoesNotExistsException extends MathematicatorException
{

	/**
	 * @var string
	 */
	private $function;

	/**
	 * FunctionDoesNotExistsException constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param \Throwable|null $previous
	 * @param string $function
	 */
	public function __construct(string $message = '', int $code = 0, \Throwable $previous = null, string $function = '')
	{
		parent::__construct($message, $code, $previous);
		$this->function = $function;
	}

	/**
	 * @return string
	 */
	public function getFunction(): string
	{
		return $this->function;
	}

}

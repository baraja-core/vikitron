<?php

namespace Mathematicator\Engine;


class DivisionByZero extends MathErrorException
{

	/**
	 * @var string[]
	 */
	private $fraction;

	/**
	 * DivisionByZero constructor.
	 *
	 * @param string $message
	 * @param int $code
	 * @param null $previous
	 * @param string[] $fraction
	 * @throws \Exception
	 */
	public function __construct($message, $code = 0, $previous = null, array $fraction)
	{
		parent::__construct($message, $code, $previous);

		if (!isset($fraction[0], $fraction[1])) {
			throw new MathematicatorException(
				'Fraction must be array [0 => INT, 1 => INT].'
				. "\n" . 'Your input: ' . json_encode($fraction)
			);
		}

		$this->fraction = $fraction;
	}

	/**
	 * @return string[]
	 */
	public function getFraction(): array
	{
		return $this->fraction;
	}

}

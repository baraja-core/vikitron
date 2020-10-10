<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Exception;


use Exception;

final class DivisionByZeroException extends MathErrorException
{

	/** @var string[] */
	private $fraction;


	/**
	 * @param string[] $fraction
	 * @throws MathematicatorException
	 */
	public function __construct(string $message, int $code = 0, Exception $previous = null, array $fraction = [])
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

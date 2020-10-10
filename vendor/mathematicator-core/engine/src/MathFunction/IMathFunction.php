<?php

declare(strict_types=1);

namespace Mathematicator\Engine\MathFunction;


interface IMathFunction
{
	/**
	 * @param mixed $haystack
	 * @param mixed ...$params
	 * @return mixed
	 */
	public function invoke($haystack, ...$params);
}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\MathFunction\Entity;


use Mathematicator\Engine\MathFunction\IMathFunction;

final class Sin implements IMathFunction
{
	public function invoke($haystack, ...$params): float
	{
		return sin((float) $haystack);
	}
}

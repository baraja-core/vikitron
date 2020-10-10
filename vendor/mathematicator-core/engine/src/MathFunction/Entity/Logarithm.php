<?php

declare(strict_types=1);

namespace Mathematicator\Engine\MathFunction\Entity;


use Mathematicator\Engine\MathFunction\IMathFunction;

final class Logarithm implements IMathFunction
{
	public function invoke($haystack, ...$params): float
	{
		return log((float) $haystack, (float) ($params[0][0] ?? 10));
	}
}

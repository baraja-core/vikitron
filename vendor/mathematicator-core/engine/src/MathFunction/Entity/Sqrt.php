<?php

declare(strict_types=1);

namespace Mathematicator\Engine\MathFunction\Entity;


use Mathematicator\Engine\MathFunction\IMathFunction;

final class Sqrt implements IMathFunction
{
	public function invoke($haystack, ...$params): float
	{
		return sqrt((float) $haystack);
	}
}

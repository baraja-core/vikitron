<?php

declare(strict_types=1);

namespace Mathematicator\Engine\MathFunction\Entity;


use Mathematicator\Engine\MathFunction\IMathFunction;

final class Cos implements IMathFunction
{
	public function invoke($haystack, ...$params): float
	{
		return cos((float) $haystack);
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\HumanString;


use Mathematicator\Numbers\IMathToolkit;
use Nette\StaticClass;
use Stringable;

/**
 * @implements IMathToolkit<MathHumanStringBuilder>
 */
final class MathHumanStringToolkit implements IMathToolkit
{
	use StaticClass;

	public const PI = 'π';

	public const DEGREE = '°';

	public const PER_MILLE = '‰';

	public const PLUS = '+';

	public const MINUS = '-';

	public const MULTIPLY = '*';

	public const DIVIDE = '/';

	public const EQUALS = '=';

	public const PERCENT = '%';


	/**
	 * @param int|string|Stringable $humanString
	 * @return MathHumanStringBuilder
	 */
	public static function create($humanString = ''): MathHumanStringBuilder
	{
		return new MathHumanStringBuilder($humanString);
	}


	/**
	 * @param int|string|Stringable $numerator
	 * @param int|string|Stringable $denominator
	 * @return MathHumanStringBuilder
	 */
	public static function frac($numerator, $denominator): MathHumanStringBuilder
	{
		return self::operator($numerator, $denominator, self::DIVIDE);
	}


	/**
	 * @param int|string|Stringable $x
	 * @param int|string|Stringable $pow
	 * @return MathHumanStringBuilder
	 */
	public static function pow($x, $pow): MathHumanStringBuilder
	{
		return new MathHumanStringBuilder($x . '^(' . $pow . ')');
	}


	/**
	 * @param int|string|Stringable $expression
	 * @param int|string|Stringable|null $n
	 * @return MathHumanStringBuilder
	 */
	public static function sqrt($expression, $n = null): MathHumanStringBuilder
	{
		return self::func('sqrt', [$expression], $n);
	}


	/**
	 * @param int|string|Stringable $content
	 * @param string $left
	 * @param string|null $right
	 * @return MathHumanStringBuilder
	 */
	public static function wrap($content, string $left, string $right = null): MathHumanStringBuilder
	{
		return new MathHumanStringBuilder($left . $content . ($right ?: $left));
	}


	/**
	 * Render function to valid human string formula.
	 *
	 * @param string $name
	 * @param array<int|string|Stringable|null> $arguments
	 * @param int|string|Stringable|null $root
	 * @return MathHumanStringBuilder
	 */
	public static function func(string $name, $arguments = [], $root = null): MathHumanStringBuilder
	{
		$return = $name;
		if ($root) {
			$return .= '[' . $root . ']';
		}
		foreach ($arguments as $argument) {
			$return .= '(' . $argument . ')';
		}

		return new MathHumanStringBuilder($return);
	}


	/**
	 * @param int|string|Stringable $left
	 * @param int|string|Stringable $right
	 * @param string $operator
	 * @return MathHumanStringBuilder
	 */
	public static function operator($left, $right, string $operator): MathHumanStringBuilder
	{
		return new MathHumanStringBuilder($left . $operator . $right);
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Latex;


use Mathematicator\Numbers\IMathToolkit;
use Nette\StaticClass;
use Stringable;

/**
 * @implements IMathToolkit<MathLatexBuilder>
 */
final class MathLatexToolkit implements IMathToolkit
{
	use StaticClass;

	public const PI = '\pi';

	public const INFINITY = '\infty';

	public const DEGREE = '\deg';

	public const PER_MILLE = '\permil';

	public const PLUS = '+';

	public const MINUS = '-';

	public const MULTIPLY = '\cdot';

	public const DIVIDE = '\div';

	public const EQUALS = '=';

	public const PERCENT = '\%';


	/**
	 * @param int|string|Stringable $latex
	 * @param string|null $delimiterLeft
	 * @param string|null $delimiterRight
	 * @return MathLatexBuilder
	 */
	public static function create($latex = '', ?string $delimiterLeft = null, ?string $delimiterRight = null): MathLatexBuilder
	{
		return new MathLatexBuilder($latex, $delimiterLeft, $delimiterRight);
	}


	/**
	 * @param int|string|Stringable $numerator
	 * @param int|string|Stringable $denominator
	 * @return MathLatexBuilder
	 */
	public static function frac($numerator, $denominator): MathLatexBuilder
	{
		return self::func('frac', [$numerator, $denominator]);
	}


	/**
	 * @param int|string|Stringable $x
	 * @param int|string|Stringable $pow
	 * @return MathLatexBuilder
	 */
	public static function pow($x, $pow): MathLatexBuilder
	{
		return new MathLatexBuilder('{' . $x . '}^{' . $pow . '}');
	}


	/**
	 * @param int|string|Stringable $expression
	 * @param int|string|Stringable|null $n
	 * @return MathLatexBuilder
	 */
	public static function sqrt($expression, $n = null): MathLatexBuilder
	{
		return self::func('sqrt', [$expression], $n);
	}


	/**
	 * @param int|string|Stringable $content
	 * @param string $left
	 * @param string|null $right
	 * @return MathLatexBuilder
	 */
	public static function wrap($content, string $left, string $right = null): MathLatexBuilder
	{
		return new MathLatexBuilder($left . $content . ($right ?: $left));
	}


	/**
	 * Render function to valid LaTeX formula.
	 *
	 * @param string $name
	 * @param array<int|string|Stringable|null> $arguments
	 * @param int|string|Stringable|null $root
	 * @return MathLatexBuilder
	 */
	public static function func(string $name, $arguments = [], $root = null): MathLatexBuilder
	{
		$return = '\\' . $name;
		if ($root) {
			$return .= '[' . $root . ']';
		}
		foreach ($arguments as $argument) {
			$return .= '{' . $argument . '}';
		}

		return new MathLatexBuilder($return);
	}


	/**
	 * @param int|string|Stringable $left
	 * @param int|string|Stringable $right
	 * @param string $operator
	 * @return MathLatexBuilder
	 */
	public static function operator($left, $right, string $operator): MathLatexBuilder
	{
		return new MathLatexBuilder($left . '\ ' . $operator . '\ ' . $right);
	}
}

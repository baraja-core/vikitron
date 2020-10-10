<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Latex;


use Stringable;

final class MathLatexToolkit
{
	public const PI = '\pi';

	public const INFINITY = '\infty';

	public const DEGREE = '\deg';

	public const PER_MILLE = '\permil';

	public const PERCENT = '\%';


	/**
	 * @param string|Stringable $latex
	 * @param string|null $delimiterLeft
	 * @param string|null $delimiterRight
	 * @return MathLatexBuilder
	 */
	public static function create($latex = '', ?string $delimiterLeft = null, ?string $delimiterRight = null): MathLatexBuilder
	{
		return new MathLatexBuilder($latex, $delimiterLeft, $delimiterRight);
	}


	/**
	 * @param string|Stringable $numerator
	 * @param string|Stringable $denominator
	 * @return MathLatexBuilder
	 */
	public static function frac($numerator, $denominator): MathLatexBuilder
	{
		return self::func('frac', [$numerator, $denominator]);
	}


	/**
	 * @param string|Stringable $x
	 * @param string|Stringable $pow
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
	 * @param string|Stringable $content
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
	 * @param string|Stringable $left
	 * @param string|Stringable $right
	 * @param string $operator
	 * @return MathLatexBuilder
	 */
	public static function operator($left, $right, string $operator): MathLatexBuilder
	{
		return new MathLatexBuilder($left . '\ ' . $operator . '\ ' . $right);
	}
}

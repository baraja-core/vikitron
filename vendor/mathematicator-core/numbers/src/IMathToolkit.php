<?php

declare(strict_types=1);

namespace Mathematicator\Numbers;


use Stringable;

/**
 * @template Builder
 */
interface IMathToolkit
{

	/**
	 * @param string|Stringable $numerator
	 * @param string|Stringable $denominator
	 * @return Builder
	 */
	public static function frac($numerator, $denominator);

	/**
	 * @param string|Stringable $x
	 * @param string|Stringable $pow
	 * @return Builder
	 */
	public static function pow($x, $pow);

	/**
	 * @param int|string|Stringable $expression
	 * @param int|string|Stringable|null $n
	 * @return Builder
	 */
	public static function sqrt($expression, $n = null);

	/**
	 * @param string|Stringable $content
	 * @param string $left
	 * @param string|null $right
	 * @return Builder
	 */
	public static function wrap($content, string $left, string $right = null);

	/**
	 * Render function to valid LaTeX formula.
	 *
	 * @param string $name
	 * @param array<int|string|Stringable|null> $arguments
	 * @param int|string|Stringable|null $root
	 * @return Builder
	 */
	public static function func(string $name, $arguments = [], $root = null);

	/**
	 * @param string|Stringable $left
	 * @param string|Stringable $right
	 * @param string $operator
	 * @return Builder
	 */
	public static function operator($left, $right, string $operator);
}

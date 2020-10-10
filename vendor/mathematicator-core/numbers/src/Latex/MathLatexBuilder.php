<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Latex;


use Mathematicator\Numbers\IMathBuilder;
use Stringable;

/**
 * @implements IMathBuilder<MathLatexBuilder>
 */
final class MathLatexBuilder implements IMathBuilder, Stringable
{

	/** @var MathLatexSnippet */
	private $snippet;


	/**
	 * @param int|string|Stringable $latex
	 * @param string|null $delimiterLeft
	 * @param string|null $delimiterRight
	 */
	public function __construct($latex = '', ?string $delimiterLeft = null, ?string $delimiterRight = null)
	{
		$this->snippet = new MathLatexSnippet((string) $latex);

		if ($delimiterLeft) {
			$this->snippet->setDelimiters($delimiterLeft, $delimiterRight);
		}
	}


	/**
	 * @return MathLatexSnippet
	 */
	public function getSnippet(): MathLatexSnippet
	{
		return $this->snippet;
	}


	public function __toString(): string
	{
		return (string) $this->snippet;
	}


	/**
	 * @param int|string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function plus($with): self
	{
		return $this->operator(MathLatexToolkit::PLUS, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function minus($with): self
	{
		return $this->operator(MathLatexToolkit::MINUS, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function multipliedBy($with): self
	{
		return $this->operator(MathLatexToolkit::MULTIPLY, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function dividedBy($with): self
	{
		return $this->operator(MathLatexToolkit::DIVIDE, $with);
	}


	/**
	 * @param int|string|Stringable $to
	 * @return MathLatexBuilder
	 */
	public function equals($to): self
	{
		return $this->operator(MathLatexToolkit::EQUALS, $to);
	}


	/**
	 * @param string $operator
	 * @param int|string|Stringable $to
	 * @return MathLatexBuilder
	 */
	public function operator(string $operator, $to): self
	{
		$this->snippet->latex = (string) MathLatexToolkit::operator($this->snippet->latex, $to, $operator);

		return $this;
	}


	/**
	 * @param string $left
	 * @param string|null $right
	 * @return self
	 */
	public function wrap(string $left, string $right = null): self
	{
		$this->snippet->latex = (string) MathLatexToolkit::wrap($this->snippet->latex, $left, $right);

		return $this;
	}
}

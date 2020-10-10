<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Latex;


use Stringable;

final class MathLatexBuilder implements Stringable
{

	/** @var MathLatexSnippet */
	private $snippet;


	/**
	 * @param string|Stringable $latex
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
	 * @param string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function plus($with): self
	{
		return $this->operator('+', $with);
	}


	/**
	 * @param string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function minus($with): self
	{
		return $this->operator('-', $with);
	}


	/**
	 * @param string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function multipliedBy($with): self
	{
		return $this->operator('\cdot', $with);
	}


	/**
	 * @param string|Stringable $with
	 * @return MathLatexBuilder
	 */
	public function dividedBy($with): self
	{
		return $this->operator('\div', $with);
	}


	/**
	 * @param string|Stringable $to
	 * @return MathLatexBuilder
	 */
	public function equals($to): self
	{
		return $this->operator('=', $to);
	}


	/**
	 * @param string $operator
	 * @param string|Stringable $to
	 * @return MathLatexBuilder
	 */
	public function operator(string $operator, $to): self
	{
		$this->snippet->latex = (string) MathLatexToolkit::operator($this->snippet->latex, $to, $operator);

		return $this;
	}


	public function wrap(string $left, string $right = null): self
	{
		$this->snippet->latex = (string) MathLatexToolkit::wrap($this->snippet->latex, $left, $right);

		return $this;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Latex;


use Stringable;

final class MathLatexSnippet implements Stringable
{

	/**
	 * @var string
	 * @internal
	 */
	public $latex;

	/** @var string|null */
	private $delimiterLeft;

	/** @var string|null */
	private $delimiterRight;


	public function __construct(string $latex = '')
	{
		$this->latex = $latex;
	}


	public function __toString(): string
	{
		return $this->delimiterLeft . $this->latex . $this->delimiterRight;
	}


	public function getLatex(): string
	{
		return $this->latex;
	}


	/**
	 * @param string $left
	 * @param string|null $right
	 * @return MathLatexSnippet
	 */
	public function setDelimiters(string $left, string $right = null): self
	{
		$this->delimiterLeft = $left;
		$this->delimiterRight = $right ?: $left;

		return $this;
	}


	/**
	 * @return string[]
	 */
	public function getDelimiters(): array
	{
		return [(string) $this->delimiterLeft, (string) $this->delimiterRight];
	}
}

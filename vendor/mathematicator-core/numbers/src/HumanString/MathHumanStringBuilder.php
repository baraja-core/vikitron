<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\HumanString;


use Mathematicator\Numbers\IMathBuilder;
use Stringable;

/**
 * @implements IMathBuilder<MathHumanStringBuilder>
 */
final class MathHumanStringBuilder implements IMathBuilder, Stringable
{

	/** @var string */
	private $humanString;


	/**
	 * @param int|string|Stringable $humanString
	 */
	public function __construct($humanString = '')
	{
		$this->humanString = (string) $humanString;
	}


	/**
	 * @return string
	 */
	public function getHumanString(): string
	{
		return $this->humanString;
	}


	public function __toString(): string
	{
		return $this->getHumanString();
	}


	/**
	 * @param int|string|Stringable $with
	 * @return self
	 */
	public function plus($with): self
	{
		return $this->operator(MathHumanStringToolkit::PLUS, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return self
	 */
	public function minus($with): self
	{
		return $this->operator(MathHumanStringToolkit::MINUS, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return self
	 */
	public function multipliedBy($with): self
	{
		return $this->operator(MathHumanStringToolkit::MULTIPLY, $with);
	}


	/**
	 * @param int|string|Stringable $with
	 * @return self
	 */
	public function dividedBy($with): self
	{
		return $this->operator(MathHumanStringToolkit::DIVIDE, $with);
	}


	/**
	 * @param int|string|Stringable $to
	 * @return self
	 */
	public function equals($to): self
	{
		return $this->operator(MathHumanStringToolkit::EQUALS, $to);
	}


	/**
	 * @param string $operator
	 * @param int|string|Stringable $to
	 * @return self
	 */
	public function operator(string $operator, $to): self
	{
		$this->humanString = (string) MathHumanStringToolkit::operator($this->humanString, $to, $operator);

		return $this;
	}


	/**
	 * @param string $left
	 * @param string|null $right
	 * @return self
	 */
	public function wrap(string $left, string $right = null): self
	{
		$this->humanString = (string) MathHumanStringToolkit::wrap($this->humanString, $left, $right);

		return $this;
	}
}

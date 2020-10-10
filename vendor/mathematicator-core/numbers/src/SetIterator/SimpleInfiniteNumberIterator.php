<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\SetIterator;


use Iterator;

/**
 * @implements Iterator<int, int>
 */
abstract class SimpleInfiniteNumberIterator implements Iterator
{
	/** @var int */
	protected $startValue;

	/** @var int */
	protected $position = 0;


	abstract public function __construct(int $startValue);


	abstract public function current(): int;


	public function key(): int
	{
		return $this->position;
	}


	public function next(): void
	{
		$this->position++;
	}


	public function rewind(): void
	{
		$this->position = 0;
	}


	public function valid(): bool
	{
		return true;
	}
}

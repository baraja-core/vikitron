<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\SetIterator;


use InvalidArgumentException;
use Iterator;

/**
 * @implements Iterator<int, int>
 */
final class OddNumberIterator extends SimpleInfiniteNumberIterator implements Iterator
{

	/**
	 * @param int $startValue Odd integer where you want to start iterate
	 */
	public function __construct(int $startValue = 1)
	{
		if ($startValue % 2 === 0) {
			throw new InvalidArgumentException("$startValue is not an odd value!");
		}

		$this->startValue = $startValue;
	}


	public function current(): int
	{
		return $this->position * 2 + $this->startValue;
	}
}

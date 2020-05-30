<?php

declare(strict_types=1);

namespace Mathematicator\Search;


interface SearchAccessor
{
	/**
	 * @return Search
	 */
	public function get(): Search;
}
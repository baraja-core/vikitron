<?php

declare(strict_types=1);

namespace Mathematicator\Search;


interface ISearchAccessor
{
	public function get(): Search;
}

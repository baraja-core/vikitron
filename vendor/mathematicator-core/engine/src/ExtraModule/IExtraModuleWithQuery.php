<?php

declare(strict_types=1);

namespace Mathematicator\Engine\ExtraModule;


interface IExtraModuleWithQuery extends IExtraModule
{
	public function setQuery(string $query): void;
}

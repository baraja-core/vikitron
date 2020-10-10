<?php

declare(strict_types=1);

namespace Mathematicator\Engine\ExtraModule;


use Mathematicator\Engine\Entity\EngineSingleResult;

interface IExtraModule
{
	public function setEngineSingleResult(EngineSingleResult $result): self;

	public function match(string $query): bool;

	public function actionDefault(): void;
}

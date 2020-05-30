<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


interface ExtraModule
{

	/**
	 * @internal
	 * @param EngineSingleResult $result
	 */
	public function setEngineSingleResult(EngineSingleResult $result): void;

	/**
	 * @param string $query
	 * @return bool
	 */
	public function match(string $query): bool;

	public function actionDefault(): void;
}
<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


abstract class BaseModule implements ExtraModule
{

	/**
	 * @var EngineSingleResult
	 */
	protected $result;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * @internal
	 * @param EngineSingleResult $result
	 */
	final public function setEngineSingleResult(EngineSingleResult $result): void
	{
		$this->result = $result;
	}

	/**
	 * @internal
	 * @param string $query
	 */
	final public function setQuery(string $query): void
	{
		$this->query = $query;
	}

}
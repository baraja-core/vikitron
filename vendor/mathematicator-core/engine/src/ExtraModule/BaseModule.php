<?php

declare(strict_types=1);

namespace Mathematicator\Engine\ExtraModule;


use Mathematicator\Engine\Entity\EngineSingleResult;
use Mathematicator\Engine\Translator;

abstract class BaseModule implements IExtraModuleWithQuery
{

	/**
	 * @var Translator
	 * @inject
	 */
	public $translator;

	/** @var EngineSingleResult */
	protected $result;

	/** @var string */
	protected $query;


	/**
	 * @internal
	 */
	final public function setEngineSingleResult(EngineSingleResult $result): IExtraModule
	{
		$this->result = $result;

		return $this;
	}


	/**
	 * @internal
	 */
	final public function setQuery(string $query): void
	{
		$this->query = $query;
	}
}

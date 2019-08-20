<?php

namespace Mathematicator\Engine;


use Mathematicator\Search\Box;

class EngineSingleResult extends EngineResult
{

	/**
	 * @var Box|null
	 */
	private $interpret;

	/**
	 * @var Box[]
	 */
	private $boxes;

	/**
	 * @var Source[]
	 */
	private $sources = [];

	/**
	 * @param string $query
	 * @param string $matchedRoute
	 * @param Box|null $interpret
	 * @param Box[] $boxes
	 * @param Source[] $sources
	 */
	public function __construct(string $query, string $matchedRoute, ?Box $interpret, array $boxes, array $sources = [])
	{
		parent::__construct($query, $matchedRoute);
		$this->interpret = $interpret;
		$this->boxes = $boxes;
		$this->sources = $sources;
	}

	/**
	 * @return Box[]
	 */
	public function getBoxes(): array
	{
		return $this->boxes;
	}

	/**
	 * @return Box|null
	 */
	public function getInterpret(): ?Box
	{
		return $this->interpret;
	}

	/**
	 * @return Source[]
	 */
	public function getSources(): array
	{
		return $this->sources;
	}

}
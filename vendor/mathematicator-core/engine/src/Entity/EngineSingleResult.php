<?php

declare(strict_types=1);

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
	private $sources;

	/**
	 * @var string[]
	 */
	private $filters;

	/**
	 * @param string $query
	 * @param string $matchedRoute
	 * @param Box|null $interpret
	 * @param Box[] $boxes
	 * @param Source[] $sources
	 * @param string[] $filters
	 */
	public function __construct(string $query, string $matchedRoute, ?Box $interpret = null, array $boxes = [], array $sources = [], array $filters = [])
	{
		parent::__construct($query, $matchedRoute);
		$this->interpret = $interpret;
		$this->boxes = $boxes;
		$this->sources = $sources;
		$this->filters = $filters;
	}

	/**
	 * @return Box[]
	 */
	public function getBoxes(): array
	{
		$withoutNoResult = [];

		foreach ($this->boxes as $box) {
			if ($box->getTag() !== 'no-results') {
				$withoutNoResult[] = $box;
			}
		}

		$return = $withoutNoResult === [] ? $this->boxes : $withoutNoResult;

		if ($this->filters !== []) {
			foreach ($return as $boxKey => $box) {
				if (\in_array($box->getTag(), $this->filters, true) === false) {
					unset($return[$boxKey]);
				}
			}
		}

		usort($return, static function (Box $a, Box $b): int {
			return $a->getRank() < $b->getRank() ? 1 : -1;
		});

		return $return;
	}

	/**
	 * @param Box $box
	 * @return EngineSingleResult
	 */
	public function addBox(Box $box): self
	{
		$this->boxes[] = $box;

		return $this;
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

	/**
	 * @param Source $source
	 * @return EngineSingleResult
	 */
	public function addSource(Source $source): self
	{
		$this->sources[] = $source;

		return $this;
	}

}

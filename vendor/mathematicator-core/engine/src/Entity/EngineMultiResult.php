<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Entity;


use Mathematicator\Engine\Exception\NoResultsException;

final class EngineMultiResult extends EngineResult
{

	/** @var EngineResult[] */
	private $results;


	/**
	 * @param string $query
	 */
	public function __construct(string $query)
	{
		parent::__construct($query, null);
	}


	/**
	 * @return EngineResult[]
	 */
	public function getResults(): array
	{
		return $this->results;
	}


	/**
	 * @return Box|null
	 */
	public function getInterpret(): ?Box
	{
		return null;
	}


	/**
	 * @return Box[]
	 */
	public function getBoxes(): array
	{
		$return = [];

		foreach ($this->getResults() as $result) {
			if ($result instanceof EngineSingleResult) {
				foreach ($result->getBoxes() as $box) {
					$return[] = $box;
				}
			}
		}

		usort($return, static function (Box $a, Box $b): int {
			return $a->getRank() < $b->getRank() ? 1 : -1;
		});

		return $return;
	}


	/**
	 * @param string|null $name
	 * @return EngineResult
	 * @throws NoResultsException
	 */
	public function getResult(string $name = null): EngineResult
	{
		if (isset($this->results[$name]) === false) {
			throw new NoResultsException('Result "' . $name . '" does not exist.');
		}

		return $this->results[$name];
	}


	/**
	 * @param EngineResult $result
	 * @param string|null $name
	 * @return EngineMultiResult
	 */
	public function addResult(EngineResult $result, ?string $name = null): self
	{
		if ($name !== null) {
			$this->results[$name] = $result;
		} else {
			$this->results[] = $result;
		}

		return $this;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Mathematicator\Search\Box;

class EngineMultiResult extends EngineResult
{

	/**
	 * @var EngineSingleResult[]
	 */
	private $results;

	/**
	 * @return EngineSingleResult[]
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
			foreach ($result->getBoxes() as $box) {
				$return[] = $box;
			}
		}

		return $return;
	}

	/**
	 * @param string|null $name
	 * @return EngineResult
	 * @throws NoResultsException
	 */
	public function getResult(string $name = null): EngineResult
	{
		if (!isset($this->results[$name])) {
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

		$this->setTime($this->getTime() + $result->getTime());

		return $this;
	}

}

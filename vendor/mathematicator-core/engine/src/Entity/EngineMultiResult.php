<?php

namespace Mathematicator\Engine;


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
	 * @param string|null $name
	 * @return EngineResult
	 * @throws NoResultsException
	 */
	public function getResult(string $name = null): EngineResult
	{
		if (!isset($this->results[$name])) {
			throw new NoResultsException('Result "' . $name . '" does not exists.');
		}

		return $this->results[$name];
	}

	/**
	 * @param EngineResult $result
	 * @param string|null $name
	 */
	public function addResult(EngineResult $result, ?string $name = null): void
	{
		if ($name !== null) {
			$this->results[$name] = $result;
		} else {
			$this->results[] = $result;
		}

		$this->setTime($this->getTime() + $result->getTime());
	}

}
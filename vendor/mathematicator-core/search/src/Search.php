<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Mathematicator\Engine\Engine;
use Mathematicator\Engine\EngineMultiResult;
use Mathematicator\Engine\EngineResult;
use Mathematicator\Engine\InvalidDataException;
use Mathematicator\Engine\NoResultsException;
use Tracy\Debugger;

class Search
{

	/**
	 * @var Engine
	 */
	private $engine;

	/**
	 * @param Engine $engine
	 */
	public function __construct(Engine $engine)
	{
		$this->engine = $engine;
	}

	/**
	 * @param string $query
	 * @return EngineResult|EngineResult[]
	 * @throws InvalidDataException|NoResultsException
	 */
	public function search(string $query)
	{
		Debugger::timer('search_request');

		if (($engineResult = $this->engine->compute($query)) instanceof EngineMultiResult) {
			return [
				'left' => $engineResult->getResult('left'),
				'right' => $engineResult->getResult('right'),
			];
		}

		return $engineResult;
	}

	/**
	 * @param string $query
	 * @return AutoCompleteResult
	 * @throws InvalidDataException|NoResultsException
	 */
	public function searchAutocomplete(string $query): AutoCompleteResult
	{
		$searchResult = $this->search($query);

		return (new AutoCompleteResult)
			->setResult(\is_array($searchResult) ? $searchResult['left'] : $searchResult);
	}

}

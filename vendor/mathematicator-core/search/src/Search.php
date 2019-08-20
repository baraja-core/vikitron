<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Mathematicator\Engine\Engine;
use Mathematicator\Engine\EngineMultiResult;
use Mathematicator\Engine\EngineResult;
use Mathematicator\Engine\NoResultsException;
use Mathematicator\Engine\QueryNormalizer;
use Tracy\Debugger;

class Search
{

	/**
	 * @var Engine
	 */
	private $engine;

	/**
	 * @var QueryNormalizer
	 */
	private $queryNormalizer;

	/**
	 * @param Engine $engine
	 * @param QueryNormalizer $queryNormalizer
	 */
	public function __construct(Engine $engine, QueryNormalizer $queryNormalizer)
	{
		$this->engine = $engine;
		$this->queryNormalizer = $queryNormalizer;
	}

	/**
	 * @param string $query
	 * @return EngineResult|EngineResult[]
	 * @throws NoResultsException
	 */
	public function search(string $query)
	{
		Debugger::timer('search_request');

		$engineResult = $this->engine->compute(
			$this->queryNormalizer->normalize($query)
		);

		if ($engineResult instanceof EngineMultiResult) {
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
	 * @throws NoResultsException
	 */
	public function searchAutocomplete(string $query): AutoCompleteResult
	{
		$searchResult = $this->search($query);

		$result = new AutoCompleteResult();
		$result->setResult(\is_array($searchResult) ? $searchResult['left'] : $searchResult);

		return $result;
	}

}

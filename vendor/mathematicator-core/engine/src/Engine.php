<?php

namespace Mathematicator\Engine;


use Mathematicator\Router\Router;
use Mathematicator\SearchController\BaseController;
use Nette\DI\Container;
use Tracy\Debugger;

class Engine
{

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @var Container
	 */
	private $serviceFactory;

	/**
	 * @param Router $router
	 * @param Container $container
	 */
	public function __construct(Router $router, Container $container)
	{
		$this->router = $router;
		$this->serviceFactory = $container;
	}

	/**
	 * @param string $query
	 * @return EngineResult
	 */
	public function compute(string $query): EngineResult
	{
		if (preg_match('/^(?<left>.+?)\s*vs\.?\s*(?<right>.+?)$/', $query, $versus)) {
			$result = new EngineMultiResult($query, null);

			$result->addResult($this->compute($versus['left']), 'left');
			$result->addResult($this->compute($versus['right']), 'right');

			return $result;
		}

		$callback = $this->router->routeQuery($query);
		$callbackResult = $this->callCallback($query, $callback);

		$result = new EngineSingleResult(
			$query,
			preg_replace('/^.+\\\\([^\\\\]+)$/', '$1', $callback),
			$callbackResult === null ? null : $callbackResult->getInterpret(),
			$callbackResult === null ? null : $callbackResult->getBoxes(),
			$callbackResult === null ? [] : $callbackResult->getSources()
		);
		$result->setTime((int) round(Debugger::timer('search_request') * 1000));

		return $result;
	}

	/**
	 * @param string $query
	 * @param string $callback
	 * @return BaseController|null
	 */
	private function callCallback(string $query, string $callback): ?BaseController
	{
		/** @var BaseController|null $return */
		$return = $this->serviceFactory->getByType($callback);

		if ($return !== null) {
			$return->setQuery($query);
			$return->resetBoxes();

			try {
				$return->actionDefault();
			} catch (TerminateException $e) {
			}
		}

		return $return;
	}

}

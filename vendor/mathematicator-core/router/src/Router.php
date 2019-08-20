<?php

declare(strict_types=1);

namespace Mathematicator\Router;

use Mathematicator\Engine\TerminateException;
use Mathematicator\SearchController\CrossMultiplicationController;
use Mathematicator\SearchController\DateController;
use Mathematicator\SearchController\ErrorTooLongController;
use Mathematicator\SearchController\NumberController;
use Mathematicator\SearchController\NumberCounterController;
use Mathematicator\SearchController\OEISController;
use Mathematicator\SearchController\OtherController;
use Mathematicator\SearchController\SequenceController;
use Nette\Utils\Strings;

class Router
{

	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var string[]
	 */
	private $functions;

	/**
	 * @param string[] $functions
	 */
	public function __construct(array $functions)
	{
		$this->functions = $functions;
	}

	/**
	 * @param string $query
	 * @return string
	 */
	public function routeQuery(string $query): string
	{
		$this->query = $query;
		$route = OtherController::class;

		try {
			$this->router();
		} catch (TerminateException $e) {
			$route = $e->getMessage();
		}

		return $route;
	}

	/**
	 * @throws TerminateException
	 */
	private function router(): void
	{
		$this->tooLongQueryRoute(ErrorTooLongController::class);
		$this->regexRoute('-?[0-9]*[.]?[0-9]+([Ee]\d+)?', NumberController::class);
		$this->regexRoute('\d+\/\d+', NumberController::class);
		$this->regexRoute('[IVXLCDMivxlcdm]{2,}', NumberController::class);
		$this->regexRoute('A\d{6}', OEISController::class);
		$this->tokenizeRoute(NumberCounterController::class);
		$this->regexRoute('now|\d{1,2}\.\d{1,2}\.\d{4}|\d{4}-\d{1,2}-\d{1,2}', DateController::class);
		$this->regexRoute('(\-?[0-9]*[.]?[0-9]+([^0-9\.\-]+)?){3,}', SequenceController::class);
		$this->keyWordRoute();
	}

	/**
	 * @param string $entity
	 * @throws TerminateException
	 */
	private function tooLongQueryRoute(string $entity): void
	{
		if (Strings::length($this->query) > 1024) {
			throw new TerminateException($entity);
		}
	}

	/**
	 * @param string $regex
	 * @param string $entity
	 * @throws TerminateException
	 */
	private function regexRoute(string $regex, string $entity): void
	{
		if (preg_match('/^' . $regex . '$/', $this->query)) {
			throw new TerminateException($entity);
		}
	}

	/**
	 * @param string $entity
	 * @throws TerminateException
	 */
	private function tokenizeRoute(string $entity): void
	{
		if (preg_match('/([\+\-\*\/\^\!])|INF|PI|<=>|<=+|>=+|!=+|=+|<>|>+|<+|(' . implode('\(|', $this->functions) . '\()/', $this->query)) {
			throw new TerminateException($entity);
		}
	}

	/**
	 * @throws TerminateException
	 */
	private function keyWordRoute(): void
	{
		$query = trim(Strings::toAscii($this->query));

		if ($query === 'trojclenka') {
			throw new TerminateException(CrossMultiplicationController::class);
		}
	}

}

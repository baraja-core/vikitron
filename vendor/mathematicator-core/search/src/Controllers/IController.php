<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\InvalidDataException;
use Mathematicator\Engine\TerminateException;
use Mathematicator\Search\Context;
use Mathematicator\Search\Query;

interface IController
{

	/**
	 * @throws TerminateException
	 */
	public function actionDefault(): void;

	/**
	 * @param Query $query
	 * @return Context
	 * @throws InvalidDataException
	 */
	public function createContext(Query $query): Context;

	/**
	 * @return Context
	 */
	public function getContext(): Context;

}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Context;
use Mathematicator\Engine\InvalidDataException;
use Mathematicator\Engine\Query;
use Mathematicator\Engine\TerminateException;

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

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Entity\Context;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Exception\InvalidDataException;
use Mathematicator\Engine\Exception\TerminateException;

interface Controller
{
	/**
	 * @throws TerminateException
	 */
	public function actionDefault(): void;

	/**
	 * @throws InvalidDataException
	 */
	public function createContext(Query $query): Context;

	public function getContext(): Context;
}

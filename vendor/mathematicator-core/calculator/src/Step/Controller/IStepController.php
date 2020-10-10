<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step\Controller;


use Mathematicator\Engine\Step\Step;
use Nette\Utils\ArrayHash;

interface IStepController
{
	/**
	 * @return Step[]
	 */
	public function actionDefault(ArrayHash $data): array;
}

<?php

declare(strict_types=1);

namespace Mathematicator\Step\Controller;


use Nette\Utils\ArrayHash;

interface IStepController
{

	public function actionDefault(ArrayHash $data): array;

}

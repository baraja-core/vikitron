<?php

namespace Model\Math\Step\Controller;

use Nette\Utils\ArrayHash;

interface IStepController
{

	public function actionDefault(ArrayHash $data): array;

}

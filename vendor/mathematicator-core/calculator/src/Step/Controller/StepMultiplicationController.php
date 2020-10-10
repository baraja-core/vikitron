<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step\Controller;


use Mathematicator\Engine\Step\Step;
use Nette\Utils\ArrayHash;
use Nette\Utils\Validators;

final class StepMultiplicationController implements IStepController
{

	/**
	 * @param ArrayHash $data
	 * @return Step[]
	 */
	public function actionDefault(ArrayHash $data): array
	{
		$steps = [];

		$x = $this->numberToFraction($data->x);
		$y = $this->numberToFraction($data->y);

		$steps[] = new Step(
			'Násobení čísel',
			null,
			'Tuto sekci teprve plánujeme.'
		); // TODO!!!

		return $steps;
	}


	/**
	 * @return string[]
	 */
	private function numberToFraction(string $number): array
	{
		if (Validators::isNumericInt($number)) {
			return [$number, '1'];
		}

		return explode('/', $number);
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Step\Controller;


use Mathematicator\Engine\Step;
use Mathematicator\Step\StepFactory;
use Nette\Utils\ArrayHash;
use Nette\Utils\Validators;

final class StepMultiplicationController implements IStepController
{

	/** @var StepFactory */
	private $stepFactory;


	/**
	 * @param StepFactory $stepFactory
	 */
	public function __construct(StepFactory $stepFactory)
	{
		$this->stepFactory = $stepFactory;
	}


	/**
	 * @param ArrayHash $data
	 * @return Step[]
	 */
	public function actionDefault(ArrayHash $data): array
	{
		$steps = [];

		$x = $this->numberToFraction($data->x);
		$y = $this->numberToFraction($data->y);

		$step = $this->stepFactory->create();
		$step->setTitle('Násobení čísel');
		$step->setDescription(
			'Tuto sekci teprve plánujeme.'
		); // TODO!!!

		$steps[] = $step;

		return $steps;
	}


	/**
	 * @param string $number
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
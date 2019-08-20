<?php

namespace Model\Math\Step\Controller;


use App\VikiTron\Model\Number\NumberHelper;
use Mathematicator\Calculator\Step;
use Model\Math\Step\StepFactory;
use Nette\Utils\ArrayHash;
use Nette\Utils\Validators;

class StepMultiplicationController implements IStepController
{

	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @var Number
	 */
	private $number;

	/**
	 * @var int
	 */
	private $tolerance = 0;

	/**
	 * @param StepFactory $stepFactory
	 * @param NumberHelper $number
	 */
	public function __construct(StepFactory $stepFactory, NumberHelper $number)
	{
		$this->stepFactory = $stepFactory;
		$this->number = $number;
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
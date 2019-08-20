<?php

namespace Model\Math\Step\Controller;

use Mathematicator\Calculator\Step;
use Model\Math\Step\StepFactory;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

class StepSqrtHelper implements IStepController
{

	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @var Step[]
	 */
	private $steps = [];

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
		bdump($data, 'Data SQRT Helper');

		if ($data->offsetExists('numberSet')) {
			$step = $this->stepFactory->create();
			$step->setDescription('N - Přirozená čísla: 1, 2, 3, 100, 105, 1006, ...');
			$this->steps[] = $step;
		}

		if ($data->offsetExists('whatBaseOfPower')) {
			$this->taskWhatBaseOfPower($data->whatBaseOfPower);
		}

		return $this->steps;
	}

	private function taskWhatBaseOfPower(int $n): void
	{
		$fittingNumber = floor(sqrt($n));
		$valueArray = [
			[$fittingNumber - 1, ($fittingNumber - 1) ** 2],
			[$fittingNumber, $fittingNumber ** 2],
			[$fittingNumber + 1, ($fittingNumber + 1) ** 2],
		];

		$step = $this->stepFactory->create();

		$outerDiv = Html::el('div');
		$outerDiv->create('style')
			->setAttribute('type', 'text/css')
			->setHtml('
			.sqrt-table-helper {
				border-collapse: separate;
				border-spacing: 2px;
				border-color: transparent;
			}
			
			.sqrt-table-helper tr td {
				border: transparent !important;
			}
		');

		$table = $outerDiv->create('table')
			->setAttribute('class', 'sqrt-table-helper');

		$tableDataEl = Html::el('td');

		$messageArray = ['Příliš málo', 'Ideální', 'Příliš moc'];

		for ($i = 0; $i < 3; $i++) {
			$tableRow = $table->create('tr');

			for ($j = 0; $j < 2; $j++) {
				$tableRow->create('td')
					->setHtml('\\(' . $valueArray[$i][$j] . ($j ? '' : '^2') . '\\)');
			}

			$tableRow->insert(1, (string) $tableDataEl->setText('...'))
				->addHtml((string) $tableDataEl->setText($messageArray[$i]));
		}

		$step->setDescription($outerDiv);

		$this->steps[] = $step;
	}

}
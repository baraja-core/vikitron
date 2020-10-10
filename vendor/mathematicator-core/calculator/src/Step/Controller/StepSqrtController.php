<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step\Controller;


use Mathematicator\Calculator\Step\StepFactory;
use Mathematicator\Engine\Step\Step;
use Nette\Utils\ArrayHash;

final class StepSqrtController implements IStepController
{

	/** @var Step[] */
	private $steps = [];


	/**
	 * @return Step[]
	 */
	public function actionDefault(ArrayHash $data): array
	{
		$n = (float) $data->n;
		$nInt = (int) $n;
		$sqrt = sqrt($n);
		$sqrtInt = (int) $sqrt;

		if (abs($sqrt - $sqrtInt) < 0.000001) {
			if ($sqrtInt <= 100) {
				$this->solveAsInteger((int) $n, $sqrtInt);
			} else {
				$this->steps[] = new Step(
					'Řešení',
					null,
					'<p>Využijeme vztahu:</p>'
					. '\(\sqrt{' . $nInt . '}\ =\ \sqrt{{' . $sqrtInt . '}^{2}}\ =\ ' . $sqrtInt . '\)'
				);
			}
		} else {
			$this->solveAsCells($n);
		}

		return $this->steps;
	}


	private function solveAsInteger(int $n, int $result): void
	{
		$table = '';

		for ($i = 0; $i <= 10; $i++) {
			$tag = $i === $result ? 'th' : 'td';

			$table .= '<tr>'
				. '<' . $tag . '>' . $i . '</' . $tag . '>'
				. '<' . $tag . '>\({' . $i . '}^{2}\ =\ ' . ($i ** 2) . '\)</' . $tag . '>'
				. '</tr>';
		}

		$step = new Step();
		$step->setTitle('Tabulka základních mocnin a odmocnin');
		$step->setDescription(
			'<p>Řešení vyhledáme v tabulce základních mocnin a odmocnin, kterou bychom si měli pamatovat.</p>'
			. '<table>'
			. '<tr><th style="width:64px">Hodnota</th><th>Druhá mocnina</th></tr>'
			. $table
			. '</table>'
		);

		$this->steps[] = $step;
	}


	private function solveAsCells(float $n): void
	{
		$cells = $this->makeCells((string) $n);

		$step = new Step();
		$step->setTitle('Rozdělení do buněk');
		$step->setDescription('Číslo musíme rozdělit po dvou směrem od desetinné čárky do buněk.');
		$step->setLatex(implode('\ |\ ', $cells));

		$this->steps[] = $step;

		$step = new Step();
		$step->setDescription('Druhá mocnina jakého přirozeného čísla nebo nula se vejde do \(' . $cells[0] . '\)?');
		$step->setAjaxEndpoint(
			StepFactory::getAjaxEndpoint(StepSqrtHelper::class, [
				'numberSet' => 'N',
				'whatBaseOfPower' => $cells[0],
			])
		);
		$step->setLatex((string) $squareRooted = floor(sqrt((float) $cells[0])));
		$this->steps[] = $step;

		$step = new Step();
		$step->setDescription('Druhou mocninu odečteme od čísla z první buňky.');
		$step->setLatex($cells[0] . ' - ' . ($squareRooted ** 2) . ' = ' . ($cells[0] - ($squareRooted ** 2)));

		$this->steps[] = $step;
	}


	/**
	 * @return string[] $cells
	 */
	private function makeCells(string $n): array
	{
		$afterDecPoint = null;

		if (strpos($n, '.')) {
			$decPointSplit = explode('.', $n);
			$beforeDecPoint = $decPointSplit[0];
			$afterDecPoint = $decPointSplit[1];
		} else {
			$beforeDecPoint = $n;
		}

		$cells = [];
		if ((($beforeLength = \strlen($beforeDecPoint)) % 2) !== 0) {
			$cells[] = $beforeDecPoint[0];
			$offset = 1;
		} else {
			$offset = 0;
		}

		for ($i = $offset; $i < $beforeLength; $i += 2) {
			$cells[] = \substr($beforeDecPoint, $i, 2);
		}

		if ($afterDecPoint !== null) {
			$afterLength = \strlen((string) $afterDecPoint);
			for ($i = 0; $i < $afterLength; $i += 2) {
				$cells[] = \substr($afterDecPoint, $i, 2);
			}

			if (\strlen((string) end($cells)) === 1) {
				$cells[\count($cells) - 1] .= '0';
			}
		}

		return $cells;
	}
}

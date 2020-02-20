<?php

declare(strict_types=1);

namespace Mathematicator\Step\Controller;


use Mathematicator\Calculator\Step;
use Mathematicator\Step\StepFactory;
use Nette\Utils\ArrayHash;
use Nette\Utils\Validators;

class StepPowController implements IStepController
{

	/**
	 * @var StepFactory
	 */
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
		if ($data['y'] === '0') {
			return $this->getYIsZero($data['x']);
		}

		if ($data['x'] > 0 && $data['y'] > 0
			&& Validators::isNumericInt($data['x']) && Validators::isNumericInt($data['y'])
		) {
			return $this->getAbsSmallIntegers((string) $data['x'], (string) $data['y']);
		}

		$steps = [];

		$step = $this->stepFactory->create(
			'Umocňování čísel',
			'{' . $data['x'] . '}^{' . $data['y'] . '}\ =\ ' . $data['result'],
			'Řešení je jen přibližné.'
		);

		$steps[] = $step;

		return $steps;
	}

	/**
	 * @param string $x
	 * @return Step[]
	 */
	private function getYIsZero(string $x): array
	{
		$steps = [];

		$step = $this->stepFactory->create();
		$step->setTitle('Uvažujme');
		$step->setDescription('\(x^0=a\) pro \(x, a \in \mathbb{R}_{-\{0\}}\)');
		$steps[] = $step;

		$step = $this->stepFactory->create();
		$step->setDescription('Nula je zajímavé číslo v tom, že jako pro jediné platí:');
		$step->setLatex('0=-0');
		$steps[] = $step;

		$step = $this->stepFactory->create();
		$step->setDescription('Díky tomuto faktu je možné tvrdit že:');
		$step->setLatex('x^0=x^{-0}');
		$steps[] = $step;

		$step = $this->stepFactory->create();
		$step->setTitle('Úprava pravé strany');
		$step->setDescription('\(x^0=\frac{1}{x^0}\) a následně \((x^0)^2=1\)');
		$steps[] = $step;

		$step = $this->stepFactory->create();
		$step->setTitle('Použití pravidel o umocňování');
		$step->setLatex('x^{0\cdot2}=1 \rightarrow x^{0}=1');
		$steps[] = $step;

		$step = $this->stepFactory->create();
		$step->setTitle('Řešení');
		$step->setDescription(
			'Pokud tedy umocňujeme jakékoliv číslo různé od nuly na nultou, výsledkem bude vždy 1.'
			. '<hr><b>Jiné vysvětlení</b>'
			. '<div class="my-3">\(x^n=\frac{x^{n+a}}{x^a},x\in \mathbb{R}_{-\{0\}},a,n\in \mathbb{R}\)</div>
			Pokud by bylo \(n = 0\), a jakékoliv reálné číslo, tak dostáváme:<br><br>
			\(x^0=\frac{x^{0+a}}{x^a}\)<br>
			\(x^0=\frac{x^{a}}{x^a}\)<br>
			\(x^0=1\)'
		);
		$steps[] = $step;

		return $steps;
	}

	/**
	 * @param string $x
	 * @param string $y
	 * @return Step[]
	 */
	private function getAbsSmallIntegers(string $x, string $y): array
	{
		$steps = [];

		$numbers = '';

		for ($i = 1; $i <= $y; $i++) {
			$numbers .= ($numbers ? '\ \cdot\ ' : '') . $x;
		}

		$step = $this->stepFactory->create();
		$step->setTitle('Řešení');
		$step->setDescription('Umocňování je operace, která vyjadřuje opakované násobení.');
		$step->setLatex('{' . $x . '}^{' . $y . '}\ =\ ' . $numbers . '\ =\ ' . bcpow($x, $y));
		$steps[] = $step;

		return $steps;
	}

}

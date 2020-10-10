<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step\Controller;


use Brick\Math\BigInteger;
use Mathematicator\Engine\Step\Step;
use Mathematicator\Numbers\Calculation;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Nette\Utils\ArrayHash;
use Nette\Utils\Validators;

final class StepPowController implements IStepController
{

	/**
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

		$steps[] = new Step(
			'Umocňování čísel',
			(string) MathLatexToolkit::pow($data['x'], $data['y'])->equals($data['result']),
			'Řešení je jen přibližné.'
		);

		return $steps;
	}


	/**
	 * @return Step[]
	 */
	private function getYIsZero(string $x): array
	{
		$steps = [];

		$step = new Step();
		$step->setTitle('Uvažujme');
		$step->setDescription('\(x^0=a\) pro \(x, a \in \mathbb{R}_{-\{0\}}\)');
		$steps[] = $step;

		$step = new Step();
		$step->setDescription('Nula je zajímavé číslo v tom, že jako pro jediné platí:');
		$step->setLatex((string) MathLatexToolkit::create('0')->equals('-0'));
		$steps[] = $step;

		$step = new Step();
		$step->setDescription('Díky tomuto faktu je možné tvrdit že:');
		$step->setLatex((string) MathLatexToolkit::pow('x', 0)->equals(MathLatexToolkit::pow('x', '-0')));
		$steps[] = $step;

		$step = new Step();
		$step->setTitle('Úprava pravé strany');

		$latex1 = MathLatexToolkit::pow('x', 0)
			->equals(
				MathLatexToolkit::frac(1, MathLatexToolkit::pow('x', 0))
			)
			->wrap('\(', '\)');

		$latex2 = MathLatexToolkit::pow(
			MathLatexToolkit::pow('x', 0)
				->wrap('(', ')'),
			2
		)
			->equals(1)
			->wrap('\(', '\)');

		$step->setDescription($latex1 . ' a následně ' . $latex2);
		$steps[] = $step;

		$step = new Step();
		$step->setTitle('Použití pravidel o umocňování');
		$step->setLatex('x^{0\cdot2}=1 \rightarrow x^{0}=1');
		$steps[] = $step;

		$step = new Step();
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
	 * @return Step[]
	 */
	private function getAbsSmallIntegers(string $x, string $y): array
	{
		$steps = [];
		$numbers = null;

		for ($i = 1; $i <= $y; $i++) {
			if ($numbers === null) {
				$numbers = MathLatexToolkit::create($x);
			} else {
				$numbers->multipliedBy($x);
			}
		}

		$steps[] = new Step(
			'Řešení',
			(string) MathLatexToolkit::create(MathLatexToolkit::pow($x, $y))
				->equals((string) $numbers)
				->equals((string) Calculation::of($x)->power(BigInteger::of($y))),
			'Umocňování je operace, která vyjadřuje opakované násobení.'
		);

		return $steps;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Equation;


use Mathematicator\Engine\Exception\MathematicatorException;

final class NewtonMethod
{

	/** @var int */
	private $steps = 0;

	/** @var float[] */
	private $intervalBuffer = [];


	/**
	 * @return float[]
	 * @throws MathematicatorException
	 */
	public function run(float $intervalLeft, float $intervalRight): array
	{
		$results = $this->iterator($intervalLeft, $intervalRight);

		echo $this->renderInterval($this->intervalBuffer);

		return $results;
	}


	/**
	 * @return float[]
	 * @throws MathematicatorException
	 */
	private function iterator(float $intervalLeft, float $intervalRight): array
	{
		$this->steps++;
		$this->intervalBuffer = [$intervalLeft, $intervalRight];
		$intervalAverage = ($intervalLeft + $intervalRight) / 2;

		$valueLeft = $this->calculator($intervalLeft);
		$valueCenter = $this->calculator($intervalAverage);
		$valueRight = $this->calculator($intervalRight);

		$isIntervalLeftPositive = $this->isPositive($valueLeft);
		$isIntervalAveragePositive = $this->isPositive($valueCenter);
		$isIntervalRightPositive = $this->isPositive($valueRight);

		if ($isIntervalLeftPositive === $isIntervalAveragePositive && $isIntervalAveragePositive === $isIntervalRightPositive) {
			throw new MathematicatorException('The equation has no solution.');
		}

		$exploreLeft = $isIntervalLeftPositive !== $isIntervalAveragePositive;
		$exploreRight = $isIntervalAveragePositive !== $isIntervalRightPositive;

		if ($exploreLeft && !$exploreRight) {
			return $this->isInTolerance($valueLeft, $valueCenter)
				? [$intervalAverage]
				: $this->iterator($intervalLeft, $intervalAverage);
		}
		if (!$exploreLeft && $exploreRight) {
			return $this->isInTolerance($valueCenter, $valueRight)
				? [$intervalAverage]
				: $this->iterator($intervalAverage, $intervalRight);
		}

		return [
			$this->iterator($intervalLeft, $intervalAverage)[0],
			$this->iterator($intervalAverage, $intervalRight)[0],
		];
	}


	private function calculator(float $x): float
	{
		return ($x - 10) * ($x - 5) * $x * ($x + 5) * ($x + 10);
	}


	private function isPositive(float $x): bool
	{
		return $x >= 0;
	}


	private function isInTolerance(float $x, float $y): bool
	{
		return abs($x - $y) < 0.00001;
	}


	/**
	 * @param float[] $items
	 * @return string
	 */
	private function renderInterval(array $items): string
	{
		if ($items === []) {
			return '';
		}

		$firstItem = $items[0];
		$otherItems = [];
		$iterator = 0;
		foreach ($items as $i) {
			if ($iterator > 0) {
				$otherItems[] = $i;
			}
			$iterator++;
		}

		return '<div style="border:1px solid #aaa;width:' . abs($firstItem * 10) . 'px">' . $firstItem . '<br>'
			. $this->renderInterval($otherItems)
			. '</div>';
	}
}

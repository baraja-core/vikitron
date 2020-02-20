<?php

declare(strict_types=1);

namespace Mathematicator;


use Mathematicator\Engine\MathematicatorException;

class NewtonMethod
{

	/**
	 * @var int
	 */
	private $steps = 0;

	/**
	 * @var array
	 */
	private $intervalBuffer = [];

	/**
	 * @param float $intervalLeft
	 * @param float $intervalRight
	 * @return float[]
	 * @throws \Exception
	 */
	public function run(float $intervalLeft, float $intervalRight)
	{
		$results = $this->iterator($intervalLeft, $intervalRight);

		dump(['steps' => $this->steps]);
		dump($this->intervalBuffer);

		echo $this->renderInterval($this->intervalBuffer);

		return $results;
	}

	private function iterator(float $intervalLeft, float $intervalRight)
	{
		$this->steps++;
		$this->intervalBuffer[] = [$intervalLeft, $intervalRight];
		$intervalAverage = ($intervalLeft + $intervalRight) / 2;

		$valueLeft = $this->calculator($intervalLeft);
		$valueCenter = $this->calculator($intervalAverage);
		$valueRight = $this->calculator($intervalRight);

		$isIntervalLeftPositive = $this->isPositive($valueLeft);
		$isIntervalAveragePositive = $this->isPositive($valueCenter);
		$isIntervalRightPositive = $this->isPositive($valueRight);

		if ($isIntervalLeftPositive === $isIntervalAveragePositive && $isIntervalAveragePositive === $isIntervalRightPositive) {
			throw new MathematicatorException('Rovnice nemá řešení.');
		}

		$exploreLeft = $isIntervalLeftPositive !== $isIntervalAveragePositive;
		$exploreRight = $isIntervalAveragePositive !== $isIntervalRightPositive;

		if ($exploreLeft && !$exploreRight) {
//			dump(['RUN' => 'LEFT', 'L' => $exploreLeft, 'R' => $exploreRight, 'from' => $valueLeft, 'to' => $valueCenter]);
			if ($this->isInTolerance($valueLeft, $valueCenter)) {
				return [$intervalAverage];
			} else {
				return $this->iterator($intervalLeft, $intervalAverage);
			}
		} elseif (!$exploreLeft && $exploreRight) {
//			dump(['RUN' => 'RIGHT', 'L' => $exploreLeft, 'R' => $exploreRight, 'from' => $valueCenter, 'to' => $valueRight]);
			if ($this->isInTolerance($valueCenter, $valueRight)) {
				return [$intervalAverage];
			} else {
				return $this->iterator($intervalAverage, $intervalRight);
			}
		} else {
//			dump(['RUN' => 'CENTER', 'L' => $exploreLeft, 'R' => $exploreRight, 'from' => $valueLeft, 'to' => $valueRight]);
			return [
				$this->iterator($intervalLeft, $intervalAverage),
				$this->iterator($intervalAverage, $intervalRight),
			];
		}
	}

	/**
	 * @param float $x
	 * @return float
	 */
	private function calculator(float $x)
	{
		return ($x - 10) * ($x - 5) * $x * ($x + 5) * ($x + 10);
//		return 2 * pow($x, 3) - 5 * $x + 2853;
	}

	/**
	 * @param float $x
	 * @return bool
	 */
	private function isPositive(float $x)
	{
		return $x >= 0;
	}

	/**
	 * @param float $x
	 * @param float $y
	 * @return bool
	 */
	private function isInTolerance(float $x, float $y)
	{
		return abs($x - $y) < 0.00001;
	}

	private function renderInterval(array $items)
	{
		if ($items === []) {
			return '';
		}
		$item = $items[0];

		$otherItems = [];
		$iterator = 0;
		foreach ($items as $i) {
			if ($iterator > 0) {
				$otherItems[] = $i;
			}
			$iterator++;
		}

		return '<div style="border:1px solid #aaa;width:' . abs($item[0] * 10) . 'px">' . $item[0] . '<br>'
			. $this->renderInterval($otherItems)
			. '</div>';
	}

}

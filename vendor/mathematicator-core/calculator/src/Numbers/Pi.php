<?php

namespace Model\Math;

class Pi
{

	public function getPi(int $countPoints = 10)
	{
		$n = 1000;
		$nu = 0;
		for ($i = 1; $i !== $n; $i++) {
			$x = $this->rnd();
			$y = $this->rnd();

			if ($x * $x + $y * $y < 1) {
				$nu += 1;
			}
		}

		dump(4 * $nu / $n);

		die;
	}

	private function rnd()
	{
		return (float) rand(1, 1e6) / 1e6;
	}

}

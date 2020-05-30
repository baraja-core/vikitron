<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


abstract class PhpGDCompiler implements Compiler
{

	/** @var resource */
	protected $image;


	/**
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return int
	 */
	protected function getColor(int $r = 0, int $g = 0, int $b = 0): int
	{
		static $cache = [];

		$key = $r . ';' . $g . ';' . $b;

		if (isset($cache[$key]) === false) {
			$cache[$key] = imagecolorallocate($this->image, $r, $g, $b);
		}

		return $cache[$key];
	}


	/**
	 * @param int[]|null $params
	 * @return int
	 */
	protected function getParameterColor(?array $params): int
	{
		if ($params === null) {
			return $this->getColor();
		}

		return $this->getColor($params[0], $params[1], $params[2]);
	}


	/**
	 * @param RenderRequest $request
	 */
	protected function process(RenderRequest $request): void
	{
		foreach ($request->getLines() as $line) {
			$this->renderLine($line);
		}
	}


	/**
	 * @param int[]|null[]|int[][] $line
	 */
	protected function renderLine(array $line): void
	{
		imageline($this->image, $line['x'], $line['y'], $line['a'], $line['b'], $this->getParameterColor($line['color'] ?? null));
	}
}
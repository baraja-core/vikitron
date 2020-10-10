<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator\Compiler;


use Mathematicator\Vizualizator\RenderRequest;

final class SvgICompiler implements ICompiler
{

	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string
	{
		$return = '';

		foreach ($request->getLines() as $line) {
			$return .= $this->renderLine($line);
		}

		$title = $request->getTitle();
		$arguments = [
			'width="' . $request->getWidth() . '"',
			'height="' . $request->getHeight() . '"',
		];

		return '<svg ' . implode(' ', $arguments) . '>'
			. ($title !== null ? '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>' : '')
			. $return
			. '</svg>';
	}


	/**
	 * @param string $name
	 * @param string[]|int[]|null[] $params
	 * @return string
	 */
	private function renderElement(string $name, array $params): string
	{
		$arguments = [];

		foreach ($params as $key => $value) {
			$arguments[] = $key . '="' . $value . '"';
		}

		return '<' . $name . ' ' . implode(' ', $arguments) . ' />';
	}


	/**
	 * @param int[]|string[]|null $params
	 * @return string
	 */
	private function getColor(?array $params): string
	{
		if ($params === null) {
			return 'rgb(0,0,0)';
		}

		return 'rgb(' . $params[0] . ',' . $params[1] . ',' . $params[2] . ')';
	}


	/**
	 * @param mixed[]|mixed[][] $line
	 * @return string
	 */
	private function renderLine(array $line): string
	{
		return $this->renderElement('line', [
			'x1' => (int) $line['x'],
			'y1' => (int) $line['y'],
			'x2' => (int) $line['a'],
			'y2' => (int) $line['b'],
			'style' => 'stroke:' . $this->getColor($line['color'] ?? null) . ';stroke-width:1',
		]);
	}
}

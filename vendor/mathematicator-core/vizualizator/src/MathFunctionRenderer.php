<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Mathematicator\Calculator\Calculator;

class MathFunctionRenderer
{

	/**
	 * @var Renderer
	 */
	private $renderer;

	/**
	 * @var Calculator
	 */
	private $calculator;

	/**
	 * @param Renderer $renderer
	 * @param Calculator $calculator
	 */
	public function __construct(Renderer $renderer, Calculator $calculator)
	{
		$this->renderer = $renderer;
		$this->calculator = $calculator;
	}

	public function plot(array $tokens, int $width = 500, int $height = 500): string
	{
		$request = new RenderRequest($this->renderer, $width, $height);

		$halfWidth = (int) ($width / 2);
		$halfHeight = (int) ($height / 2);

		$request->addLine(0, $halfHeight, $width, $halfHeight);
		$request->addLine($halfWidth, 0, $halfWidth, $height);

		// TODO: Use $this->calculator for rendering line.

		return $request->render();
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Mathematicator\Tokenizer\Token\IToken;

final class MathFunctionRenderer
{

	/** @var Renderer */
	private $renderer;


	public function __construct(Renderer $renderer)
	{
		$this->renderer = $renderer;
	}


	/**
	 * @param IToken[] $tokens
	 * @return string
	 */
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

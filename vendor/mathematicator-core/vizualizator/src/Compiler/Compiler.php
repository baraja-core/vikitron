<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


interface Compiler
{
	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string;
}
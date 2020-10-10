<?php

declare(strict_types=1);

namespace Mathematicator\MandelbrotSet;


use Nette\DI\CompilerExtension;

final class MandelbrotSetExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('mandelbrotSet'))
			->setFactory(MandelbrotSet::class, [
				'tempDir' => '%tempDir%/mandelbrot-set', // TODO: Check it
			]);
	}
}

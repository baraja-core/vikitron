<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Nette\DI\CompilerExtension;

final class VizualizatorExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('mathFunctionRenderer'))
			->setFactory(MathFunctionRenderer::class);

		$builder->addDefinition($this->prefix('renderer'))
			->setFactory(Renderer::class);
	}
}

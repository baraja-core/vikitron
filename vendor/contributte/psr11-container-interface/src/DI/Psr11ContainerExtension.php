<?php declare(strict_types = 1);

namespace Contributte\Psr11\DI;

use Contributte\Psr11\Container;
use Nette\DI\CompilerExtension;

class Psr11ContainerExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('container'))
			->setType(Container::class);
	}

}

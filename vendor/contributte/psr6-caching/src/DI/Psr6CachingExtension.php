<?php declare(strict_types = 1);

namespace Contributte\Psr6\DI;

use Contributte\Psr6\CachePoolFactory;
use Contributte\Psr6\ICachePoolFactory;
use Nette\DI\CompilerExtension;

class Psr6CachingExtension extends CompilerExtension
{

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('factory'))
			->setFactory(CachePoolFactory::class)
			->setType(ICachePoolFactory::class);
	}

}

<?php declare(strict_types = 1);

namespace Contributte\Psr6;

interface ICachePoolFactory
{

	public function create(string $namespace): CachePool;

}

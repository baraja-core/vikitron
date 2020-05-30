<?php declare(strict_types = 1);

namespace Contributte\Psr6;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class CachePoolFactory implements ICachePoolFactory
{

	/** @var IStorage */
	private $storage;

	public function __construct(IStorage $storage)
	{
		$this->storage = $storage;
	}

	public function create(string $namespace): CachePool
	{
		return new CachePool(new Cache($this->storage, $namespace));
	}

}

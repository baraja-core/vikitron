<?php declare(strict_types = 1);

namespace Contributte\Psr6;

use Closure;
use Contributte\Psr6\Exception\CacheException;
use Contributte\Psr6\Exception\InvalidArgumentException;
use Nette\Caching\Cache;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Throwable;

class CachePool implements CacheItemPoolInterface
{

	/** @var Cache */
	protected $internal;

	/** @var Closure */
	protected $createItem;

	public function __construct(Cache $cache)
	{
		$this->internal = $cache;

		$this->createItem = Closure::bind(
			static function (string $key, $value) {
				$item = new CacheItem();
				$item->key = $key;
				$item->value = $value;
				$item->hit = $value !== null;

				return $item;
			},
			null,
			CacheItem::class
		);
	}

	/**
	 * @param string $key
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getItem($key): CacheItemInterface
	{
		self::assertKey($key);

		try {
			return ($this->createItem)($key, $this->internal->load($key));
		} catch (Throwable $e) {
			throw new CacheException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param string[] $keys
	 * @return iterable|CacheItem[]
	 */
	public function getItems(array $keys = []): iterable
	{
		array_map(sprintf('%s::assertKey', self::class), $keys);

		try {
			$items = $this->internal->bulkLoad($keys);

			return array_map($this->createItem, array_keys($items), $items);
		} catch (Throwable $e) {
			throw new CacheException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param string $key
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function hasItem($key): bool
	{
		return $this->getItem($key)->isHit();
	}

	public function clear(): bool
	{
		try {
			$this->internal->clean([Cache::ALL]);
		} catch (Throwable $e) {
			throw new CacheException($e->getMessage(), $e->getCode(), $e);
		}

		return true;
	}

	/**
	 * @param string $key
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function deleteItem($key): bool
	{
		return $this->deleteItems([$key]);
	}

	/**
	 * @param string[] $keys
	 */
	public function deleteItems(array $keys): bool
	{
		array_map(sprintf('%s::assertKey', self::class), $keys);

		try {
			foreach ($keys as $key) {
				$this->internal->remove($key);
			}
		} catch (Throwable $e) {
			throw new CacheException($e->getMessage(), $e->getCode(), $e);
		}

		return true;
	}

	public function save(CacheItemInterface $item): bool
	{
		if (!($item instanceof CacheItem)) {
			throw new InvalidArgumentException(
				sprintf('Invalid type "%s" for $item', get_class($item))
			);
		}

		try {
			$this->internal->save($item->getKey(), $item->get(), $item->getDependencies());
		} catch (Throwable $e) {
			throw new CacheException($e->getMessage(), $e->getCode(), $e);
		}

		return true;
	}

	/**
	 * Nette cache has no bulk saving
	 */
	public function saveDeferred(CacheItemInterface $item): bool
	{
		return $this->save($item);
	}

	/**
	 * Nette cache has no bulk saving
	 */
	public function commit(): bool
	{
		return true;
	}

	private static function assertKey(string $key): void
	{
		if (!is_string($key)) {
			throw new InvalidArgumentException(
				sprintf('Invalid type "%s" for $key', gettype($key))
			);
		}

		if ($key === '') {
			throw new InvalidArgumentException('$key must not be empty');
		}
	}

}

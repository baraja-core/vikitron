<?php declare(strict_types = 1);

namespace Contributte\Psr11;

use Contributte\Psr11\Exception\Generic\ContainerException;
use Contributte\Psr11\Exception\Logic\ServiceNotFoundException;
use Nette\DI\Container as NetteContainer;
use Nette\DI\MissingServiceException;
use Psr\Container\ContainerInterface;
use Throwable;

class Container implements ContainerInterface
{

	/** @var NetteContainer */
	private $container;

	public function __construct(NetteContainer $container)
	{
		$this->container = $container;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @throws ServiceNotFoundException  No entry was found for **this** identifier.
	 * @throws ContainerException Error while retrieving the entry.
	 * @return mixed Entry.
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function get($id)
	{
		try {
			if (class_exists($id) || interface_exists($id)) {
				return $this->container->getByType($id);
			}
			return $this->container->getService($id);
		} catch (MissingServiceException $e) {
			throw new ServiceNotFoundException($e);
		} catch (Throwable $e) {
			throw new ContainerException($e);
		}
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function has($id): bool
	{
		if (class_exists($id) || interface_exists($id)) {
			return $this->container->getByType($id, false) !== null;
		}
		return $this->container->hasService($id);
	}

}

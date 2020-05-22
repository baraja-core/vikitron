<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Nette\DI\Container;

/**
 * @deprecated use DIC extension or event handle to Nette Application.
 */
interface IAfterInstall
{

	/**
	 * @param Container $container
	 * @param PackageRegistrator $packageRegistrator
	 */
	public function __construct(Container $container, PackageRegistrator $packageRegistrator);

	/**
	 * Return true if some one was changed.
	 *
	 * @return bool
	 */
	public function run(): bool;
}
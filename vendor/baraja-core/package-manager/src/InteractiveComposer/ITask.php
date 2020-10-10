<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Exception\TaskException;
use Baraja\PackageManager\PackageRegistrator;

interface ITask
{
	public function __construct(PackageRegistrator $packageRegistrator);

	/**
	 * Return true if task was ok.
	 *
	 * @return bool
	 * @throws TaskException
	 */
	public function run(): bool;

	public function getName(): string;
}

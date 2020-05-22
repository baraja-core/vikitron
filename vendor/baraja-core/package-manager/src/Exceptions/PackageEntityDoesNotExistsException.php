<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Exception;


final class PackageEntityDoesNotExistsException extends PackageDescriptorException
{

	/**
	 * @throws PackageEntityDoesNotExistsException
	 */
	public static function packageDescriptionEntityDoesNotExist(): void
	{
		throw new self('Package description entity does not exist.');
	}
}
<?php declare(strict_types = 1);

namespace Contributte\Psr11\Exception\Generic;

use Contributte\Psr11\Exception\GenericException;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

class ContainerException extends GenericException implements ContainerExceptionInterface
{

	public function __construct(Throwable $previous)
	{
		parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
	}

}

<?php declare(strict_types = 1);

namespace Contributte\Psr11\Exception\Logic;

use Contributte\Psr11\Exception\LogicalException;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ServiceNotFoundException extends LogicalException implements NotFoundExceptionInterface
{

	public function __construct(Throwable $previous)
	{
		parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
	}

}

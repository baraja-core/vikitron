<?php declare(strict_types = 1);

namespace Contributte\Psr6\Exception;

use Psr\Cache\InvalidArgumentException as Psr6InvalidArgumentException;

class InvalidArgumentException extends CacheException implements Psr6InvalidArgumentException
{

}

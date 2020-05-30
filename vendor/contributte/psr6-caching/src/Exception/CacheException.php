<?php declare(strict_types = 1);

namespace Contributte\Psr6\Exception;

use Psr\Cache\CacheException as Psr6CacheException;

class CacheException extends LogicException implements Psr6CacheException
{

}

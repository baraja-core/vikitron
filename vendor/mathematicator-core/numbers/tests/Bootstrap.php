<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests;


use Tester\Environment;

if (\is_file($autoload = __DIR__ . '/../vendor/autoload.php')) {
	require_once $autoload;
	Environment::setup();
}

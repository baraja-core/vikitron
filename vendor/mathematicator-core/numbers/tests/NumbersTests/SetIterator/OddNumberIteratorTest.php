<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\SetIterator;


use Mathematicator\Numbers\SetIterator\OddNumberIterator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class OddNumberIteratorTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$iterator = new OddNumberIterator();
		Assert::same(1, $iterator->current());
		$iterator->next();
		Assert::same(3, $iterator->current());
		$iterator->next();
		Assert::same(5, $iterator->current());
		$iterator->rewind();
		Assert::same(1, $iterator->current());
	}
}

(new OddNumberIteratorTest())->run();

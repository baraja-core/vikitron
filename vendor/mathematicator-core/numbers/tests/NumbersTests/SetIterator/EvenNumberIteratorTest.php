<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests\SetIterator;


use Mathematicator\Numbers\SetIterator\EvenNumberIterator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../Bootstrap.php';

class EvenNumberIteratorTest extends TestCase
{
	public function testMultipliedBy(): void
	{
		$iterator = new EvenNumberIterator();
		Assert::same(0, $iterator->current());
		$iterator->next();
		Assert::same(2, $iterator->current());
		$iterator->next();
		Assert::same(4, $iterator->current());
		$iterator->rewind();
		Assert::same(0, $iterator->current());
	}
}

(new EvenNumberIteratorTest())->run();

<?php

declare(strict_types=1);

namespace Mathematicator\Numbers\Tests;


use Mathematicator\Numbers\SmartNumber;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../Bootstrap.php';

class SmartNumberTest extends TestCase
{
	public function testEntity(): void
	{
		$smartNumber = new SmartNumber(0, '10');
		Assert::same('10', $smartNumber->getInteger());
	}
}

(new SmartNumberTest())->run();

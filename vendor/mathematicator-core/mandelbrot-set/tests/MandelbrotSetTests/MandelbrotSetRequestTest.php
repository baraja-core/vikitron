<?php

declare(strict_types=1);

namespace Mathematicator\MandelbrotSet\Tests;


use Mathematicator\MandelbrotSet\MandelbrotSetRequest;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class MandelbrotSetRequestTest extends TestCase
{
	public function testEntity(): void
	{
		$request = new MandelbrotSetRequest(10, 10);
		Assert::same('300_300_18_-2_1_-1_1_10_10.png', $request->getFileName());
	}
}

Bootstrap::boot();
(new MandelbrotSetRequestTest())->run();

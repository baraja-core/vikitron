<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator\Tests;


use Mathematicator\Vizualizator\Renderer;
use Mathematicator\Vizualizator\RenderRequest;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class RenderRequestTest extends TestCase
{
	public function testEntity(): void
	{
		$request = new RenderRequest(new Renderer(), 100, 100);
		Assert::same(100, $request->getWidth());
	}
}

Bootstrap::boot();
(new RenderRequestTest())->run();

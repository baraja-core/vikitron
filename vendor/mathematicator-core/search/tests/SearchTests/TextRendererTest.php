<?php

declare(strict_types=1);

namespace Mathematicator\Search\Tests;


use Mathematicator\Search\TextRenderer;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class TextRendererTest extends TestCase
{
	public function testProcess(): void
	{
		Assert::same('&lt;a href=\'test\'&gt;Test&lt;/a&gt;', TextRenderer::process("<a href='test'>Test</a>"));
	}
}

(new TextRendererTest())->run();

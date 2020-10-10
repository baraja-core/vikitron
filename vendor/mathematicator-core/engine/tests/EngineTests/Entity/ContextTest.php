<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Entity;


use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Context;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Entity\Source;
use Mathematicator\Engine\Exception\TerminateException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class ContextTest extends TestCase
{
	public function testContent(): void
	{
		$context = new Context($query = new Query('1+2', '1+2'));

		Assert::same('1+2', $context->getQuery());
		Assert::same($query, $context->getQueryEntity());
	}


	public function testBoxes(): void
	{
		$context = new Context($query = new Query('1+2', '1+2'));

		Assert::same([], $context->getBoxes());

		$box = $context->addBox(Box::TYPE_TEXT);

		Assert::same([$box], $context->getBoxes());

		$context->resetBoxes();

		Assert::same([], $context->getBoxes());
	}


	public function testBoxesLimit(): void
	{
		Assert::exception(function () {
			$context = new Context($query = new Query('1+2', '1+2'));

			for ($i = 0; $i <= Context::BOXES_LIMIT + 10; $i++) {
				$context->addBox(Box::TYPE_TEXT);
			}
		}, TerminateException::class);
	}


	public function testSources(): void
	{
		$context = new Context($query = new Query('1+2', '1+2'));
		$context->addSource($source = new Source('My source'));

		Assert::same([$source], $context->getSources());
	}


	public function testInterpret(): void
	{
		$context = new Context($query = new Query('1+2', '1+2'));
		$box = $context->setInterpret(Box::TYPE_TEXT, '1+2');

		Assert::same($box, $context->getInterpret());
	}


	public function testDynamicConfiguration(): void
	{
		$context = new Context($query = new Query('1+2', '1+2'));
		$myConfigA = $context->getDynamicConfiguration('my-config');
		$myConfigB = $context->getDynamicConfiguration('my-config');

		Assert::same($myConfigA, $myConfigB);
		Assert::same(['my-config' => $myConfigA], $context->getDynamicConfigurations());
	}


	public function testLink(): void
	{
		// Real request simulation
		$_SERVER['REQUEST_URI'] = '/kontakt';
		$_SERVER['HTTP_HOST'] = 'baraja.cz';
		$_SERVER['HTTPS'] = 'on';

		$context = new Context($query = new Query('1+2', '1+2'));

		Assert::same('https://baraja.cz/search', $context->link(''));
		Assert::same('https://baraja.cz/search?q=hello', $context->link('hello'));
		Assert::same('https://baraja.cz/search?q=1%2B2', $context->link('1+2'));
	}
}

(new ContextTest)->run();

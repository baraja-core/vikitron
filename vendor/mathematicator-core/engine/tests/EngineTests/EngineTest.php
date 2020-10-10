<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests;


use Mathematicator\Engine\Engine;
use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\EngineMultiResult;
use Mathematicator\Engine\Entity\EngineSingleResult;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class EngineTest extends TestCase
{

	/** @var Engine */
	private $engine;


	public function __construct(Container $container)
	{
		$this->engine = $container->getByType(Engine::class);
	}


	public function testSimpleCompute(): void
	{
		Assert::type(EngineSingleResult::class, $this->engine->compute('5+3'));
	}


	public function testVersus(): void
	{
		Assert::type(EngineMultiResult::class, $this->engine->compute('1+1 vs. 5+3'));
		Assert::type(EngineMultiResult::class, $this->engine->compute('1+1 vs 5+3'));
		Assert::type(EngineMultiResult::class, $this->engine->compute('1+1 versus 5+3'));
	}


	public function testExtraModule(): void
	{
		/** @var EngineSingleResult $result */
		$result = $this->engine->compute('help');

		Assert::type(EngineSingleResult::class, $result);

		$box = $result->getBoxes()[0];

		Assert::type(Box::class, $box);
		Assert::same('Help', $box->getTitle());
		Assert::same('What can I help you with?', $box->getText());
	}


	public function testOtherController(): void
	{
		/** @var EngineSingleResult $result */
		$result = $this->engine->compute('abcd unknown query... 1234');

		Assert::count(1, $result->getBoxes());
	}
}

(new EngineTest(Bootstrap::boot()))->run();

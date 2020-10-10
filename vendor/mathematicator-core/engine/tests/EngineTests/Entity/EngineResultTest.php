<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Entity;


use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\EngineMultiResult;
use Mathematicator\Engine\Entity\EngineSingleResult;
use Mathematicator\Engine\Entity\Source;
use Mathematicator\Engine\Exception\NoResultsException;
use Mathematicator\Engine\Router\DynamicRoute;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class EngineResultTest extends TestCase
{
	public function testSingleResult(): void
	{
		$interpret = new Box(Box::TYPE_TEXT, 'Interpret', '5+3*2');
		$box = new Box(Box::TYPE_TEXT, 'Result', '11');
		$source = new Source('Baraja');
		$result = new EngineSingleResult('5+3*2', DynamicRoute::class, $interpret, [$box], [$source]);

		Assert::same('5+3*2', $result->getQuery());
		Assert::same(5, $result->getLength());
		Assert::type('float', $result->getTime());
		Assert::same(DynamicRoute::class, $result->getMatchedRoute());
		Assert::same($interpret, $result->getInterpret());
		Assert::same([$box], $result->getBoxes());
		Assert::same([$source], $result->getSources());

		$result->addSource($source);
		Assert::same([$source, $source], $result->getSources());
	}


	public function testMultiResult(): void
	{
		$singleResultA = new EngineSingleResult('5+3*2', DynamicRoute::class);
		$singleResultB = new EngineSingleResult('1+1', DynamicRoute::class);

		$result = new EngineMultiResult('5+3*2 vs 1+1');

		Assert::exception(function () use ($result) {
			$result->getResult('unknown-result');
		}, NoResultsException::class);

		$result->addResult($singleResultA);
		$result->addResult($singleResultB, 'second');

		$boxA = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxA->setRank(10);

		$boxB = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxB->setRank(50);

		$singleResultA->addBox($boxA);
		$singleResultB->addBox($boxB);

		Assert::same(null, $result->getInterpret());
		Assert::same(null, $result->getMatchedRoute());
		Assert::same([$boxB, $boxA], $result->getBoxes());
		Assert::same($singleResultB, $result->getResult('second'));
		Assert::same([$singleResultA, 'second' => $singleResultB], $result->getResults());
	}


	public function testAddBox(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'Result', '11');
		$result = new EngineSingleResult('5+3*2', DynamicRoute::class);

		Assert::same([], $result->getBoxes());

		$result->addBox($box);
		Assert::same([$box], $result->getBoxes());
	}


	public function testAddNoResultBox(): void
	{
		$boxNoResult = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxNoResult->setTag('no-results');
		$boxNoResult->setRank(50);

		$box = new Box(Box::TYPE_TEXT, 'Result', '11');
		$box->setRank(10);

		$boxWithTag = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxWithTag->setRank(5);
		$boxWithTag->setTag('result');

		$result = new EngineSingleResult('5+3*2', DynamicRoute::class);

		$result->addBox($box);
		$result->addBox($boxNoResult);

		// Return all boxes with real content
		Assert::same([$box], $result->getBoxes(), 'Real content');

		$result->addBox($boxWithTag);

		// Filter all results without "result"
		$result->addFilter('result');
		Assert::same([$boxWithTag], $result->getBoxes(), 'Filter boxes without tag');
	}


	public function testBoxSorting(): void
	{
		$boxA = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxA->setRank(10);

		$boxB = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxB->setRank(50);

		$boxC = new Box(Box::TYPE_TEXT, 'Result', '11');
		$boxC->setRank(30);

		$result = new EngineSingleResult('5+3*2', DynamicRoute::class);
		$result->addBox($boxA);
		$result->addBox($boxB);
		$result->addBox($boxC);

		Assert::same([$boxB, $boxC, $boxA], $result->getBoxes());
	}
}

(new EngineResultTest)->run();

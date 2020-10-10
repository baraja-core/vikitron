<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Entity;


use InvalidArgumentException;
use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Step\Step;
use Nette\Utils\Json;
use RuntimeException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class BoxTest extends TestCase
{
	public function testSimpleText(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'Result', 'Result does not exist.');

		Assert::same(Box::TYPE_TEXT, $box->getType());
		Assert::same('Result', $box->getTitle());
		Assert::same('Result does not exist.', $box->getText());
	}


	public function testSimpleTextToString(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'Result', 'Result does not exist.');

		Assert::same('Result does not exist.', (string) $box);
	}


	public function testIcon(): void
	{
		// No icon
		$boxText = new Box(Box::TYPE_TEXT);
		Assert::same('<i class="fas fa-hashtag"></i>', $boxText->getIcon());


		// No icon but image
		$boxImage = new Box(Box::TYPE_IMAGE);
		Assert::same('<i class="fas fa-image"></i>', $boxImage->getIcon());


		$boxCustomIcon = new Box(Box::TYPE_TEXT);
		$boxCustomIcon->setIcon('fas fa-abcd');
		Assert::same('<i class="fas fa-abcd"></i>', $boxCustomIcon->getIcon());


		Assert::exception(function () {
			$boxInvalidCustomIcon = new Box(Box::TYPE_TEXT);
			$boxInvalidCustomIcon->setIcon('invalid icon name');
		}, RuntimeException::class);
	}


	public function testTable(): void
	{
		$table = [
			['One', 'Two', 'Three'],
			[1, 2, 3],
		];

		$box = new Box(Box::TYPE_TABLE, 'Result');
		$box->setTable($table);

		Assert::same(Json::encode($table), $box->getText());
	}


	public function testTableToString(): void
	{
		$box = new Box(Box::TYPE_TABLE, 'Result');

		Assert::same('', (string) $box);
	}


	public function testKeyValue(): void
	{
		$box = new Box(Box::TYPE_TABLE, 'Result');
		$box->setKeyValue([
			'Name' => 'Eiffel Tower',
			'Height' => '300 m',
		]);

		$code = '<table>'
			. '<tr><th style="width:33%">Name:</th><td>Eiffel Tower</td></tr>'
			. '<tr><th>Height:</th><td>300 m</td></tr>'
			. '</table>';

		Assert::same($code, $box->getText());
	}


	public function testUrl(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'No result');
		Assert::same(null, $box->getUrl());


		$box = new Box(Box::TYPE_TEXT, 'No result', '...', 'https://baraja.cz');
		Assert::same('https://baraja.cz', $box->getUrl());
	}


	public function testSteps(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'Result');

		$stepA = new Step('First step', null, 'Let\'s start with...');

		// First empty step state
		Assert::same([], $box->getSteps());

		// Add first step
		$box->addStep($stepA);

		// Check step array with one Step entity
		Assert::same([$stepA], $box->getSteps());

		// Set invalid step entity
		Assert::exception(function () use ($box) {
			$query = new Query('1+1', '1+1');
			$box->setSteps([$query]);
		}, InvalidArgumentException::class);

		// Check valid steps array
		$box->setSteps([$stepA, $stepA]);
		Assert::same([$stepA, $stepA], $box->getSteps());
	}


	public function testTag(): void
	{
		$box = new Box(Box::TYPE_TEXT, 'No result');
		$box->setTag('no-results');

		Assert::same('no-results', $box->getTag());
	}


	public function testRank(): void
	{
		$box = new Box(Box::TYPE_TEXT);

		$box->setRank(25);
		Assert::same(25, $box->getRank());

		$box->setRank(150);
		Assert::same(100, $box->getRank());

		$box->setRank(-1);
		Assert::same(0, $box->getRank());
	}
}

(new BoxTest)->run();

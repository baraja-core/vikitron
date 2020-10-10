<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Step;


use Mathematicator\Engine\Step\Step;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class StepTest extends TestCase
{
	public function testStep(): void
	{
		$step = new Step('Add 2 numbers', '1 + 1', 'Use an addition operation.');

		Assert::same('Add 2 numbers', $step->getTitle());
		Assert::same('1 + 1', $step->getLatex());
		Assert::same('Use an addition operation.', $step->getDescription());

		$step->setTitle('New title');
		Assert::same('New title', $step->getTitle());
		Assert::same(false, $step->isHtmlTitle());

		$step->setTitle('New title', true);
		Assert::same('New title', $step->getTitle());
		Assert::same(true, $step->isHtmlTitle());

		$step->setLatex('\pi');
		Assert::same('\pi', $step->getLatex());

		$step->setDescription('New description');
		Assert::same('New description', $step->getDescription());
		Assert::same(false, $step->isHtmlDescription());

		$step->setDescription('New description', true);
		Assert::same('New description', $step->getDescription());
		Assert::same(true, $step->isHtmlDescription());

		Assert::same(null, $step->getAjaxEndpoint());
		$step->setAjaxEndpoint('endpoint-name');
		Assert::same('endpoint-name', $step->getAjaxEndpoint());
	}
}

(new StepTest)->run();

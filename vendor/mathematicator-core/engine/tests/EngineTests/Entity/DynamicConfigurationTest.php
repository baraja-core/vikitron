<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Entity;


use Mathematicator\Engine\Entity\DynamicConfiguration;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class DynamicConfigurationTest extends TestCase
{
	public function testTranslator(): void
	{
		$config = new DynamicConfiguration('my-config');

		Assert::same('my-config', $config->getKey());
		Assert::same(null, $config->getTitle());

		$config->setTitle('My configuration');
		Assert::same('My configuration', $config->getTitle());

		Assert::same([], $config->getLabels());

		$config->addLabel('key', 'Value');
		Assert::same(['key' => 'Value'], $config->getLabels());
		Assert::same('Value', $config->getLabel('key'));
		Assert::same('unknown', $config->getLabel('unknown'));

		$config->addLabel('key', null);
		Assert::same([], $config->getLabels());

		$config->setValues([
			'a' => '1',
			'b' => '2',
		]);

		$config->setValue('x', '256');
		$config->setValue('y', '512');

		Assert::equal(['a' => '1', 'b' => '2', 'x' => '256', 'y' => '512'], $config->getValues());

		Assert::same('256', $config->getValue('x'));
		Assert::same('unknown', $config->getValue('myValue', 'unknown'));

		Assert::equal(['a' => '1', 'b' => '2', 'x' => '256', 'y' => '512', 'myValue' => 'unknown'], $config->getValues());

		Assert::same('a=1&b=2&x=256&y=512', $config->getSerialized());
	}
}

(new DynamicConfigurationTest)->run();

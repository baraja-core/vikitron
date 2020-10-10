<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests;


use Mathematicator\Engine\Translator;
use Nette\DI\Container;
use Symfony\Component\Translation\TranslatorInterface;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class TranslatorTest extends TestCase
{

	/** @var TranslatorInterface */
	private $translator;


	public function __construct(Container $container)
	{
		$this->translator = $container->getByType(Translator::class);
	}


	public function testTranslate(): void
	{
		// Check simple translation
		Assert::same('Test', $this->translator->translate('test.test'));

		// Named parameters
		Assert::same('The number is 5.', $this->translator->translate('test.withParam', ['number' => 5]));

		// Sequential parameters
		Assert::same('Sample FIRST | THIRD | SECOND', $this->translator->translate('test.sequentialParams.sample', 'FIRST', 'SECOND', 'THIRD'));
	}
}

$container = (new Bootstrap())::boot();
(new TranslatorTest($container))->run();

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Translation;


use Mathematicator\Engine\Tests\Bootstrap;
use Mathematicator\Engine\Translation\TranslatorHelper;
use Nette\DI\Container;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class TranslationTest extends TestCase
{

	/** @var TranslatorInterface */
	private $translator;


	public function __construct(Container $container)
	{
		$this->translator = $container->getByType(TranslatorHelper::class)->translator;
	}


	public function testTranslate(): void
	{
		// Check simple translation
		Assert::same('Ale ne!', $this->translator->trans('ohNo', [], 'engine', 'cs_CZ'));
		Assert::same('Oh no!', $this->translator->trans('ohNo', [], 'engine', 'en_US'));

		// Check translation with parameter
		Assert::same('The number is 5.', $this->translator->trans('withParam', ['%number%' => 5], 'test', 'en_US'));

		// Check default language
		Assert::same('Oh no!', $this->translator->trans('ohNo', [], 'engine'));

		// Test fallback
		Assert::same('Nepřeloženo', $this->translator->trans('testFallback', [], 'test'));

		// Test hierarchy
		Assert::same('Child of a parent.', $this->translator->trans('parent.child', [], 'test'));
	}
}

$container = (new Bootstrap())::boot();
(new TranslationTest($container))->run();

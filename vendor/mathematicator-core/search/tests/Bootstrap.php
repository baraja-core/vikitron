<?php

declare(strict_types=1);

namespace Mathematicator\Search\Tests;


use Mathematicator\Engine\Translation\TranslatorHelper;
use Nette\Configurator;
use Nette\DI\Container;
use Tester\Environment;

if (\is_file($autoload = __DIR__ . '/../vendor/autoload.php')) {
	require_once $autoload;
	Environment::setup();
}

class Bootstrap
{
	public static function boot(): Container
	{
		$configurator = new Configurator();

		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');

		$configurator->createRobotLoader()
			->addDirectory(__DIR__ . '/../src')
			->register();

		$configurator
			->addConfig(__DIR__ . '/../common.neon');

		$container = $configurator->createContainer();

		/** @var TranslatorHelper $translatorHelper */
		$translatorHelper = $container->getByType(TranslatorHelper::class);

		// Package translations
		$translatorHelper->addResource(__DIR__ . '/../translations', 'search');

		// Set default language to english for tests for better understandability.
		$translatorHelper->translator->setLocale('en_US');

		return $container;
	}
}

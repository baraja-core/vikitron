<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Mathematicator\Engine\Controller\ErrorTooLongController;
use Mathematicator\Engine\Controller\OtherController;
use Mathematicator\Engine\ExtraModule\SampleModule;
use Mathematicator\Engine\Formatter\NaturalTextFormatter;
use Mathematicator\Engine\Router\Router;
use Mathematicator\Engine\Translation\TranslatorHelper;
use Nette\DI\CompilerExtension;

final class EngineExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('translator'))
			->setFactory(Translator::class);

		$builder->addDefinition($this->prefix('translatorHelper'))
			->setFactory(TranslatorHelper::class);

		$builder->addDefinition($this->prefix('sampleModule'))
			->setFactory(SampleModule::class);

		$builder->addDefinition($this->prefix('engine'))
			->setFactory(Engine::class)
			->addSetup('?->addExtraModule(?)', ['@self', '@' . SampleModule::class]);

		$builder->addDefinition($this->prefix('queryNormalizer'))
			->setFactory(QueryNormalizer::class);

		$builder->addDefinition($this->prefix('numberRewriter'))
			->setFactory(NumberRewriter::class);

		$builder->addDefinition($this->prefix('naturalTextFormatter'))
			->setFactory(NaturalTextFormatter::class);

		$builder->addDefinition($this->prefix('router'))
			->setFactory(Router::class);

		$builder->addDefinition($this->prefix('otherController'))
			->setFactory(OtherController::class)
			->setAutowired(OtherController::class);

		$builder->addDefinition($this->prefix('errorTooLongController'))
			->setFactory(ErrorTooLongController::class)
			->setAutowired(ErrorTooLongController::class);
	}
}

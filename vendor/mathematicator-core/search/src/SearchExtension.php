<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Nette\DI\CompilerExtension;

final class SearchExtension extends CompilerExtension
{

	/** @var string[] */
	public static $controllers = [
		'\Mathematicator\Search\Controller\NumberController',
		'\Mathematicator\Search\Controller\DateController',
		'\Mathematicator\Search\Controller\NumberCounterController',
		'\Mathematicator\Search\Controller\IntegralController',
		'\Mathematicator\Search\Controller\SequenceController',
		'\Mathematicator\Search\Controller\OEISController',
		'\Mathematicator\Search\Controller\CrossMultiplicationController',
		'\Mathematicator\Search\Controller\MandelbrotSetController',
		'\Mathematicator\Search\Controller\TreeController',
	];


	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('search'))
			->setFactory(Search::class);

		$builder->addAccessorDefinition($this->prefix('searchAccessor'))
			->setImplement(ISearchAccessor::class);

		$builder->addDefinition($this->prefix('console'))
			->setFactory(Console::class);

		$builder->addDefinition($this->prefix('renderer'))
			->setFactory(Renderer::class);

		// TODO: Implement autoregister for controllers
		foreach (self::$controllers as $controller) {
			$builder->addDefinition($this->prefix('controller') . '.' . md5($controller))
				->setFactory($controller)
				->setAutowired($controller);
		}
	}
}

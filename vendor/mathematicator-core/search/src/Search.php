<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Mathematicator\Engine\Engine;
use Mathematicator\Engine\Entity\EngineMultiResult;
use Mathematicator\Engine\Entity\EngineResult;
use Mathematicator\Engine\Entity\EngineSingleResult;
use Mathematicator\Engine\Exception\InvalidDataException;
use Mathematicator\Engine\Exception\NoResultsException;
use Mathematicator\Engine\Router\DynamicRoute;
use Mathematicator\Engine\Router\Router;
use Mathematicator\Engine\Translation\TranslatorHelper;
use Mathematicator\Engine\Translator;
use Mathematicator\Search\Controller\CrossMultiplicationController;
use Mathematicator\Search\Controller\DateController;
use Mathematicator\Search\Controller\IntegralController;
use Mathematicator\Search\Controller\MandelbrotSetController;
use Mathematicator\Search\Controller\NumberController;
use Mathematicator\Search\Controller\NumberCounterController;
use Mathematicator\Search\Controller\OEISController;
use Mathematicator\Search\Controller\SequenceController;
use Mathematicator\Search\Controller\TreeController;
use Mathematicator\Search\Entity\AutoCompleteResult;
use Mathematicator\Search\Entity\Result;
use Nette\Localization\ITranslator;
use Tracy\Debugger;

class Search
{

	/** @var Engine */
	private $engine;

	/** @var ITranslator */
	private $translator;

	/** @var TranslatorHelper */
	private $translatorHelper;


	public function __construct(Engine $engine, Router $router, TranslatorHelper $translatorHelper, Translator $translator)
	{
		$this->engine = $engine;
		$this->translatorHelper = $translatorHelper;
		$this->translator = $translator;

		$translatorHelper->addResource(__DIR__ . '/../translations', 'search');

		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, '(?:strom|tree)\s+.+', TreeController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, 'integr(?:a|á)l\s+.+', IntegralController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, '-?[0-9]*[.]?[0-9]+([Ee]\d+)?', NumberController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, '\d+\/\d+', NumberController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, '[IVXLCDMivxlcdm]{2,}', NumberController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_STATIC, ['pi', 'ludolfovo cislo'], NumberController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_STATIC, ['inf'], NumberCounterController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, 'A\d{6}', OEISController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_TOKENIZE, null, NumberCounterController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, 'now|\d{1,2}\.\d{1,2}\.\d{4}|\d{4}-\d{1,2}-\d{1,2}', DateController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, '(\-?[0-9]*[.]?[0-9]+([^0-9\.\-]+)?){3,}', SequenceController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_STATIC, ['mandelbrotova mnozina', 'mandelbrot set'], MandelbrotSetController::class));
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_STATIC, ['trojclenka'], CrossMultiplicationController::class));
	}


	/**
	 * Set language for translator.
	 */
	public function setLocale(string $lang): void
	{
		$this->translatorHelper->translator->setLocale($lang);
	}


	/**
	 * @return EngineResult|EngineResult[]
	 * @throws InvalidDataException|NoResultsException
	 */
	public function search(string $query)
	{
		if (class_exists('\Tracy\Debugger')) {
			Debugger::timer('search_request');
		}
		if (($engineResult = $this->engine->compute($query)) instanceof EngineMultiResult) {
			return [
				'left' => $engineResult->getResult('left'),
				'right' => $engineResult->getResult('right'),
			];
		}

		return $engineResult;
	}


	/**
	 * @throws InvalidDataException|NoResultsException
	 */
	public function searchAutocomplete(string $query): AutoCompleteResult
	{
		$searchResult = $this->search($query);
		/** @var EngineSingleResult $resultEntity */
		$resultEntity = \is_array($searchResult) ? $searchResult['left'] : $searchResult;

		return (new AutoCompleteResult())
			->setResult((new Result())->setBoxes($resultEntity->getBoxes()));
	}
}

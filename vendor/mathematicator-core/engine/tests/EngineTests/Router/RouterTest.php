<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Router;


use Mathematicator\Engine\Controller\ErrorTooLongController;
use Mathematicator\Engine\Controller\OtherController;
use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Context;
use Mathematicator\Engine\Entity\DynamicConfiguration;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Entity\Source;
use Mathematicator\Engine\Router\DynamicRoute;
use Mathematicator\Engine\Router\Router;
use Mathematicator\Engine\Tests\Bootstrap;
use Mathematicator\Engine\Translator;
use Nette\DI\Container;
use RuntimeException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class RouterTest extends TestCase
{

	/** @var Container */
	private $container;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	public function testWithoutRoutes(): void
	{
		$router = new Router;

		Assert::same(OtherController::class, $router->routeQuery('1+1'));
	}


	public function testTooLongQuery(): void
	{
		$router = new Router;
		$query = str_repeat('1+', Query::LENGTH_LIMIT + 10) . '1';
		$controller = $router->routeQuery($query);

		Assert::same(ErrorTooLongController::class, $controller);

		/** @var ErrorTooLongController $errorTooLongController */
		$errorTooLongController = $this->container->getByType($controller);
		$errorTooLongController->translator = $this->container->getByType(Translator::class);
		$errorTooLongController->createContext($queryEntity = new Query($query, $query));
		$errorTooLongController->actionDefault();

		Assert::same($query, $errorTooLongController->getQuery());
		Assert::same($queryEntity, $errorTooLongController->getQueryEntity());
		Assert::same('/search', $errorTooLongController->linkToSearch(''));
		Assert::same('/search?q=1', $errorTooLongController->linkToSearch('1'));
		Assert::type(DynamicConfiguration::class, $errorTooLongController->getDynamicConfiguration('my-config'));

		$errorTooLongController->addSource($source = new Source);
		Assert::same([$source], $errorTooLongController->getContext()->getSources());

		$boxes = $errorTooLongController->getContext()->getBoxes();
		Assert::type('array', $boxes);
		Assert::count(1, $boxes);

		$box = $boxes[0];

		Assert::same(Box::TYPE_TEXT, $box->getType());
		Assert::same('Too long query', $box->getTitle());
		Assert::same('<i class="fas fa-exclamation-triangle"></i>', $box->getIcon());
		Assert::same('no-results', $box->getTag());
	}


	public function testRegexRoute(): void
	{
		$router = new Router;
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_REGEX, 'now|today', 'TimeController'));

		Assert::same(OtherController::class, $router->routeQuery('yesterday'));
		Assert::same('TimeController', $router->routeQuery('now'));
	}


	public function testStaticRoute(): void
	{
		$router = new Router;
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_STATIC, ['now'], 'TimeController'));

		Assert::same(OtherController::class, $router->routeQuery('yesterday'));
		Assert::same('TimeController', $router->routeQuery('now'));
	}


	public function testTokenizeRoute(): void
	{
		$router = new Router;
		$router->addDynamicRoute(new DynamicRoute(DynamicRoute::TYPE_TOKENIZE, '', 'CalculatorController'));

		Assert::same(OtherController::class, $router->routeQuery('now'));
		Assert::same('CalculatorController', $router->routeQuery('5+3'));
	}


	public function testDynamicConfigurationFromUrl(): void
	{
		$router = new Router;
		$controllerClass = $router->routeQuery('1+1');

		Assert::same(OtherController::class, $controllerClass);

		/** @var OtherController $controller */
		$controller = $this->container->getByType($controllerClass);

		Assert::type(OtherController::class, $controller);

		$_GET['myConfig_x'] = '34';
		$_GET['myConfig_y'] = '256';
		$_GET['second-parameter'] = 'hello';
		$controller->createContext(new Query('1+1', '1+1'));
		$controller->translator = $this->container->getByType(Translator::class);
		$dynamicConfigurations = $controller->getContext()->getDynamicConfigurations();

		Assert::count(1, $dynamicConfigurations);

		$dynamicConfiguration = $dynamicConfigurations['myConfig'];

		Assert::same(['myConfig' => $dynamicConfiguration], $dynamicConfigurations);
		Assert::same('34', $dynamicConfiguration->getValue('x'));
		Assert::same('256', $dynamicConfiguration->getValue('y'));

		$controller->addBoxDynamicConfiguration('myConfig');

		$_SERVER['HTTP_HOST'] = 'baraja.cz';
		$_SERVER['REQUEST_URI'] = '/kontakt';
		$_SERVER['HTTPS'] = 'on';

		$controller->addBoxDynamicConfiguration('myConfig');
	}


	public function testMaxBoxLimitation(): void
	{
		$router = new Router;
		$controllerClass = $router->routeQuery('1+1');
		/** @var OtherController $controller */
		$controller = $this->container->getByType($controllerClass);

		$controller->createContext(new Query('1+1', '1+1'));

		Assert::exception(function () use ($controller) {
			for ($i = 0; $i <= Context::BOXES_LIMIT + 10; $i++) {
				$controller->addBox(Box::TYPE_TEXT);
			}
		}, RuntimeException::class);
	}


	public function testCustomInterpret(): void
	{
		$router = new Router;
		$controllerClass = $router->routeQuery('1+1');
		/** @var OtherController $controller */
		$controller = $this->container->getByType($controllerClass);

		$controller->createContext(new Query('1+1', '1+1'));

		$interpret = $controller->setInterpret(Box::TYPE_TEXT, '1+1');

		Assert::same('Interpretace zadání dotazu', $interpret->getTitle());
	}
}

$container = (new Bootstrap())::boot();
(new RouterTest($container))->run();

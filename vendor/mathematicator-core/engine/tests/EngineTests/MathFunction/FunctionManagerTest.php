<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\MathFunction;


use Mathematicator\Engine\MathFunction\Entity\Sin ;
use Mathematicator\Engine\MathFunction\FunctionManager;
use RuntimeException;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class FunctionManagerTest extends TestCase
{
	public function testBasic(): void
	{
		$sin = FunctionManager::getFunction('sin');

		Assert::type(Sin::class, $sin);
		Assert::type('array', FunctionManager::getFunctions());
		Assert::type('array', FunctionManager::getFunctionNames());
		Assert::contains('sin', FunctionManager::getFunctionNames());
		Assert::same('1', (string) $sin->invoke(M_PI_2));

		FunctionManager::addFunction('my-function', new Sin);

		Assert::exception(function () {
			FunctionManager::addFunction('cos', new Sin);
		}, RuntimeException::class);

		Assert::exception(function () {
			FunctionManager::getFunction('unknown-function');
		}, RuntimeException::class);
	}


	public function testBasicCalculator(): void
	{
		Assert::same('1', (string) FunctionManager::invoke('sin', M_PI_2), 'sin(PI/2)');
		Assert::same('1', (string) FunctionManager::invoke('cos', 0), 'cos(0)');
		Assert::same('0', (string) FunctionManager::invoke('tan', 0), 'tan(0)');
		Assert::same('4', (string) FunctionManager::invoke('sqrt', 16), 'sqrt(16)');
		Assert::same('2', (string) FunctionManager::invoke('log', 100), 'log(100)');
		Assert::same('2', (string) FunctionManager::invoke('log', 100, 10), 'log(100)');
		Assert::same('1', (string) FunctionManager::invoke('ln', M_E, M_E), 'ln(e)');
	}
}

(new FunctionManagerTest)->run();

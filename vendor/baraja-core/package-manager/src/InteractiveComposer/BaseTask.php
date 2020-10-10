<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Helpers;
use Baraja\PackageManager\PackageRegistrator;
use Nette\Configurator;
use Nette\DI\Container;

abstract class BaseTask implements ITask
{

	/** @var PackageRegistrator */
	protected $packageRegistrator;


	final public function __construct(PackageRegistrator $packageRegistrator)
	{
		$this->packageRegistrator = $packageRegistrator;
	}


	/**
	 * @param string $question
	 * @param string[] $possibilities
	 * @return string|null
	 */
	public function ask(string $question, array $possibilities = []): ?string
	{
		return Helpers::terminalInteractiveAsk($question, $possibilities);
	}


	/**
	 * Try boot Nette application and create DIC.
	 * This container is same for all tasks.
	 *
	 * Warning: When you boot application, you can not modify configuration neon data.
	 *
	 * @return Container
	 */
	final public function getContainer(): Container
	{
		/** @var Container|null $container */
		static $container;

		if ($container === null) {
			if (\is_dir($rootDir = dirname(__DIR__, 5)) === false) {
				throw new \RuntimeException('Root dir "' . $rootDir . '" does not exist.');
			}
			$application = $this->bootApplication();
			$application->addParameters([ // hack for Nette default parameters resolver
				'rootDir' => $rootDir,
				'appDir' => $rootDir . '/app',
				'wwwDir' => $rootDir . '/www',
				'vendorDir' => $rootDir . '/vendor',
				'tempDir' => $rootDir . '/temp',
			]);
			$container = $application->createContainer();
		}

		return $container;
	}


	private function bootApplication(): Configurator
	{
		foreach (['\App\Bootstrap', '\App\Booting'] as $class) {
			if (\class_exists($class) === true) {
				return $class::boot();
			}
		}

		throw new \RuntimeException('Nette application does not exist, because class "Booting" or "Bootstrap" does not found.');
	}
}

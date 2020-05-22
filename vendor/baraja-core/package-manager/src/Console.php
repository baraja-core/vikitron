<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Contributte\Console\Application;
use Nette\Application\IPresenter;
use Nette\Application\Responses\VoidResponse;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Tracy\Debugger;

final class Console
{

	/** @var Container|null */
	private static $container;


	/**
	 * @internal called by DIC.
	 * Simple console wrapper for call internal command by index.php.
	 */
	public static function run(): void
	{
		if (self::$container === null) {
			echo 'Error: Container was not set.';

			return;
		}

		if (isset($_SERVER['NETTE_TESTER_RUNNER']) === false && class_exists(Application::class)) {
			try {
				/** @var Application $application */
				$application = self::$container->getByType(Application::class);

				$runCode = $application->run();
				echo "\n" . 'Exit with code #' . $runCode;
				exit($runCode);
			} catch (\Throwable $e) {
				Debugger::log($e);
				$logPath = \dirname(__DIR__, 4) . '/log/exception.log';

				if (\is_file($logPath) === true) {
					$data = file($logPath);
					$logLine = trim((string) ($data[\count($data) - 1] ?? '???'));

					if (preg_match('/(exception--[\d-]+--[a-f\d]+\.html)/', $logLine, $logLineParser)) {
						Helpers::terminalRenderError('Logged to file: ' . $logLineParser[1]);
					}

					Helpers::terminalRenderError($logLine);
					Helpers::terminalRenderCode($e->getFile(), $e->getLine());
				} else {
					Helpers::terminalRenderError($e->getMessage());
					Helpers::terminalRenderCode($e->getFile(), $e->getLine());
				}

				$exitCode = $e->getCode() ?: 1;
				echo "\n" . 'Exit with code #' . $exitCode;
				exit($exitCode);
			}
		}
	}


	/**
	 * @internal
	 * @param Container $container
	 */
	public static function setContainer(Container $container): void
	{
		/** @var \Nette\Application\Application $application */
		$application = $container->getByType(\Nette\Application\Application::class);

		$application->onPresenter[] = function (\Nette\Application\Application $application, IPresenter $presenter): void {
			if ($presenter instanceof Presenter) {
				$presenter->onStartup[] = function (Presenter $presenter): void {
					$presenter->sendResponse(new VoidResponse);
				};
			}
		};

		self::$container = $container;
	}
}
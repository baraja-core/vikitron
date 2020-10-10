<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Contributte\Console\Application;
use Nette\Application\Application as NetteApplication;
use Tracy\Debugger;

final class Console
{
	public function __construct(Application $consoleApplication, NetteApplication $netteApplication)
	{
		$netteApplication->onStartup[] = function (NetteApplication $application) use ($consoleApplication): void {
			$this->run($consoleApplication);
		};
	}


	/**
	 * Simple console wrapper for call internal command by index.php.
	 *
	 * @param Application $consoleApplication
	 */
	private function run(Application $consoleApplication): void
	{
		try {
			$consoleApplication->setAutoExit(false);
			$runCode = $consoleApplication->run();
			echo "\n" . 'Exit with code #' . $runCode;
			exit($runCode);
		} catch (\Throwable $e) {
			if (\class_exists(Debugger::class) === true) {
				Debugger::log($e, 'critical');
			}
			if (\is_file($logPath = \dirname(__DIR__, 4) . '/log/exception.log') === true) {
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

			echo "\n" . 'Exit with code #' . ($exitCode = $e->getCode() ?: 1);
			exit($exitCode);
		}
	}
}

<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Composer\AssetsFromPackageTask;
use Baraja\PackageManager\Composer\ClearCacheTask;
use Baraja\PackageManager\Composer\ComposerJsonTask;
use Baraja\PackageManager\Composer\ConfigLocalNeonTask;
use Baraja\PackageManager\Composer\ITask;
use Baraja\PackageManager\Exception\TaskException;

class InteractiveComposer
{

	/**
	 * @var string[]
	 */
	private static $tasks = [
		ConfigLocalNeonTask::class,
		AssetsFromPackageTask::class,
		ComposerJsonTask::class,
		ClearCacheTask::class,
	];

	/**
	 * @var PackageRegistrator
	 */
	private $packageRegistrator;

	public function __construct(PackageRegistrator $packageRegistrator)
	{
		$this->packageRegistrator = $packageRegistrator;
	}

	/**
	 * Class must implement ITask.
	 *
	 * @param string $taskClass
	 */
	public static function addTask(string $taskClass): void
	{
		self::$tasks[] = $taskClass;
	}

	public function run(): void
	{
		$errorTasks = [];

		foreach (self::$tasks as $task) {
			echo "\n" . str_repeat('-', 100) . "\n";

			/** @var ITask $taskInstance */
			$taskInstance = new $task($this->packageRegistrator);

			echo "\e[0;32;40m" . '🍏 Task: ' . $taskInstance->getName() . "\e[0m\n";

			try {
				if ($taskInstance->run() === true) {
					echo "\n\n" . '👍 ' . "\e[1;33;40m" . 'Task was successful. 👍' . "\e[0m";
				} else {
					$errorTasks[] = $task;
					echo "\n\n" . 'Task error.';
				}
			} catch (TaskException $e) {
				$errorTasks[] = $task;
				echo "\n\n" . 'Task error (' . $e->getMessage() . ').';
			}
		}

		echo "\n" . str_repeat('-', 100) . "\n\n\n";

		if (\count($errorTasks) > 0) {
			echo 'Error tasks:' . "\n\n";

			foreach ($errorTasks as $errorTask) {
				echo '- ' . $errorTask . "\n";
			}
		} else {
			echo 'All tasks was OK.';
		}

		echo "\n\n\n";
	}

}
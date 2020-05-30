<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Composer\CompanyIdentity;
use Baraja\PackageManager\Composer\ITask;
use Baraja\PackageManager\Exception\TaskException;
use Tracy\Debugger;

final class InteractiveComposer
{

	/** @var PackageRegistrator */
	private $packageRegistrator;


	public function __construct(PackageRegistrator $packageRegistrator)
	{
		$this->packageRegistrator = $packageRegistrator;
	}


	public function run(): void
	{
		foreach ($this->getTasks() as $taskItem) {
			$taskClass = $taskItem[0];
			echo "\n" . str_repeat('-', 100) . "\n";

			/** @var ITask $task */
			$task = new $taskClass($this->packageRegistrator);

			echo "\e[0;32;40m" . '🍏 [' . $taskItem[1] . ']: ' . $task->getName() . "\e[0m\n";

			try {
				if ($task->run() === true) {
					echo "\n\n" . '👍 ' . "\e[1;33;40m" . 'Task was successful. 👍' . "\e[0m";
				} else {
					echo "\n\n";
					Helpers::terminalRenderError('Task "' . $taskClass . '" failed!');
					echo "\n\n";
					die;
				}
			} catch (TaskException | \RuntimeException $e) {
				echo "\n\n";
				Helpers::terminalRenderError('Task "' . $taskClass . '" failed!' . "\n\n" . $e->getMessage());
				if (\class_exists(Debugger::class) === true) {
					Debugger::log($e, 'critical');
					echo "\n\n" . 'Error was logged by Tracy.';
				} else {
					echo "\n\n" . 'Can not log error, because Tracy does not exist.';
				}
				echo "\n\n";
				die;
			}
		}

		echo "\n" . str_repeat('-', 100) . "\n\n\n" . 'All tasks completed successfully.' . "\n\n\n";
	}


	/**
	 * @return string[][]|int[][]
	 */
	private function getTasks(): array
	{
		$return = [];
		$startTime = microtime(true);
		echo 'Indexing classes...';
		$identityTemplate = null;

		foreach (array_keys(ClassMapGenerator::createMap($this->packageRegistrator->getProjectRoot())) as $className) {
			if (\is_string($className) === false) {
				throw new \RuntimeException('Class name must be type of string, but type "' . \gettype($className) . '" given.');
			}

			if (preg_match('/^[A-Z0-9].*Task$/', $className)) {
				try {
					$ref = new \ReflectionClass($className);
					if ($ref->isInterface() === false && $ref->isAbstract() === false && $ref->implementsInterface(ITask::class) === true) {
						$return[$className] = [
							$className,
							($doc = $ref->getDocComment()) !== false && preg_match('/Priority:\s*(\d+)/', $doc, $docParser) ? (int) $docParser[1] : 10,
						];
					}
				} catch (\ReflectionException $e) {
				}
			}

			if (preg_match('/^[A-Z0-9].*Identity/', $className)) {
				try {
					$ref = new \ReflectionClass($className);
					if ($ref->isInterface() === false && $ref->isAbstract() === false && $ref->implementsInterface(CompanyIdentity::class) === true) {
						/** @var CompanyIdentity $identity */
						$identity = $ref->newInstance();
						$identityTemplate = $identity->getLogo();
					}
				} catch (\ReflectionException $e) {
				}
			}
		}
		echo ' (' . number_format((microtime(true) - $startTime) * 1000, 2, '.', ' ') . ' ms)' . "\n";
		if ($identityTemplate !== null) {
			echo $identityTemplate . "\n";
		}

		usort($return, static function (array $a, array $b): int {
			return $a[1] < $b[1] ? 1 : -1;
		});

		return $return;
	}
}

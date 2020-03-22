<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Composer\CompanyIdentity;
use Baraja\PackageManager\Composer\ITask;
use Baraja\PackageManager\Exception\TaskException;

final class InteractiveComposer
{

	/**
	 * @var PackageRegistrator
	 */
	private $packageRegistrator;

	public function __construct(PackageRegistrator $packageRegistrator)
	{
		$this->packageRegistrator = $packageRegistrator;
	}

	public function run(): void
	{
		foreach ($this->getTasks() as $taskClass) {
			echo "\n" . str_repeat('-', 100) . "\n";

			/** @var ITask $task */
			$task = new $taskClass($this->packageRegistrator);

			echo "\e[0;32;40m" . '🍏 Task: ' . $task->getName() . "\e[0m\n";

			try {
				if ($task->run() === true) {
					echo "\n\n" . '👍 ' . "\e[1;33;40m" . 'Task was successful. 👍' . "\e[0m";
				} else {
					echo "\n\n";
					Helpers::terminalRenderError('Task "' . $taskClass . '" failed!');
					echo "\n\n";
					die;
				}
			} catch (TaskException|\RuntimeException $e) {
				echo "\n\n";
				Helpers::terminalRenderError('Task "' . $taskClass . '" failed!' . "\n\n" . $e->getMessage());
				echo "\n\n";
				die;
			}
		}

		echo "\n" . str_repeat('-', 100) . "\n\n\n" . 'All tasks was OK.' . "\n\n\n";
	}

	/**
	 * @return string[]
	 */
	private function getTasks(): array
	{
		$return = [];
		echo 'Indexing classes...' . "\n";

		foreach (ClassMapGenerator::createMap($this->packageRegistrator->getProjectRoot()) as $class => $path) {
			if (preg_match('/^[A-Z0-9].*Task$/', $class)) {
				try {
					$ref = new \ReflectionClass($class);
					if ($ref->isInterface() === false && $ref->isAbstract() === false && $ref->implementsInterface(ITask::class) === true) {
						$return[$class] = [
							$class,
							($doc = $ref->getDocComment()) !== false && preg_match('/Priority:\s*(\d+)/', $doc, $docParser) ? (int) $docParser[1] : 10,
						];
					}
				} catch (\ReflectionException $e) {
				}
			}

			if (preg_match('/^[A-Z0-9].*Identity/', $class)) {
				try {
					$ref = new \ReflectionClass($class);
					if ($ref->isInterface() === false && $ref->isAbstract() === false && $ref->implementsInterface(CompanyIdentity::class) === true) {
						/** @var CompanyIdentity $identity */
						$identity = $ref->newInstance();

						echo $identity->getLogo() . "\n" . str_repeat('-', 100) . "\n";
					}
				} catch (\ReflectionException $e) {
				}
			}
		}
		echo "\n";

		usort($return, static function (array $a, array $b): int {
			return $a[1] < $b[1] ? 1 : -1;
		});

		return array_map(static function (array $haystack): string {
			return $haystack[0];
		}, $return);
	}

}

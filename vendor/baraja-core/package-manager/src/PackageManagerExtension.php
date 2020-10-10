<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Contributte\Console\Application;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;

final class PackageManagerExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition('baraja.packageRegistrator')
			->setFactory(PackageRegistrator::class)
			->setAutowired(PackageRegistrator::class);
	}


	public function afterCompile(ClassType $class): void
	{
		if (PHP_SAPI === 'cli' && class_exists(Application::class) === true) {
			$class->getMethod('initialize')->addBody(
				'// Package manager.' . "\n"
				. '(function () {' . "\n"
				. "\t" . 'if (isset($_SERVER[\'NETTE_TESTER_RUNNER\']) === true) { return; }' . "\n"
				. "\t" . 'new ' . Console::class . '($this->getByType(?), $this->getByType(?));' . "\n"
				. '})();' . "\n",
				[Application::class, \Nette\Application\Application::class]
			);
		}
	}
}

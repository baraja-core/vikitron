<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;
use Baraja\PackageManager\Exception\PackageDescriptorException;
use Baraja\PackageManager\Exception\PackageEntityDoesNotExistsException;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Tracy\Debugger;

class PackageRegistrator
{

	/** @var bool */
	private static $created = false;

	/** @var string */
	private static $projectRoot;

	/** @var string[]|null */
	private static $parameters;

	/** @var string */
	private static $configPath;

	/** @var string */
	private static $configPackagePath;

	/** @var string */
	private static $configLocalPath;

	/** @var PackageDescriptorEntity */
	private static $packageDescriptorEntity;

	/** @var bool */
	private static $runAfterScripts = false;

	/** @var true[] */
	private static $neonNoUseParams = [
		'includes' => true,
		'application' => true,
		'routers' => true,
		'afterInstall' => true,
	];


	/**
	 * @param string|null $projectRoot
	 * @param string|null $tempPath
	 */
	public function __construct(?string $projectRoot = null, ?string $tempPath = null)
	{
		if (self::$created === true || $projectRoot === null || $tempPath === null) {
			return;
		}

		self::$created = true;
		self::$projectRoot = rtrim($projectRoot, '/');
		self::$configPath = self::$projectRoot . '/app/config/common.neon';
		self::$configPackagePath = self::$projectRoot . '/app/config/package.neon';
		self::$configLocalPath = self::$projectRoot . '/app/config/local.neon';

		try {
			$storage = new Storage($tempPath);
			try {
				self::$packageDescriptorEntity = $storage->load();

				if ($this->isCacheExpired(self::$packageDescriptorEntity)) {
					throw new PackageEntityDoesNotExistsException('Cache expired');
				}
			} catch (PackageEntityDoesNotExistsException $e) {
				$storage->save(
					self::$packageDescriptorEntity = (new Generator($projectRoot, $this->getPackageNamePatterns(), $storage))->run(),
					$this->getComposerHash()
				);
				$this->createPackageConfig(self::$packageDescriptorEntity);
				self::$runAfterScripts = true;
			}
		} catch (PackageDescriptorException $e) {
			Debugger::log($e);
			Helpers::terminalRenderError($e->getMessage());
		}
	}


	public static function composerPostAutoloadDump(): void
	{
		try {
			(new InteractiveComposer(new self(__DIR__ . '/../../../../', __DIR__ . '/../../../../temp/')))->run();
		} catch (\Exception $e) {
			Helpers::terminalRenderError($e->getMessage());
			Helpers::terminalRenderCode($e->getFile(), $e->getLine());
			Debugger::log($e);
			echo 'Error was logged to file.' . "\n\n";
		}
	}


	/**
	 * @return PackageDescriptorEntity
	 */
	public static function getPackageDescriptorEntityStatic(): PackageDescriptorEntity
	{
		return self::$packageDescriptorEntity;
	}


	/**
	 * @internal
	 * @return mixed[]
	 */
	public function getParameters(): array
	{
		if (self::$parameters === null && isset(($configNeon = Neon::decode(file_get_contents(self::$configPath)))['parameters'])) {
			self::$parameters = $configNeon['parameters'];
		}

		return self::$parameters ?? [];
	}


	/**
	 * @return string[]
	 */
	public function getPackageNamePatterns(): array
	{
		$return = [];
		$packageRegistrator = $this->getParameters()['packageRegistrator'] ?? null;
		$key = 'customPackagesNamePatterns';

		if (isset($packageRegistrator[$key]) === true && \is_array($packageRegistrator[$key]) === true) {
			foreach (\array_merge($return, $packageRegistrator[$key]) as $item) {
				if (\is_string($item) === true) {
					$return[] = $item;
				}
			}
		}

		return array_unique(array_merge(['^baraja-'], $return));
	}


	/**
	 * @return string
	 */
	public function getProjectRoot(): string
	{
		return self::$projectRoot;
	}


	/**
	 * @return PackageDescriptorEntity
	 */
	public function getPackageDescriptorEntity(): PackageDescriptorEntity
	{
		return self::$packageDescriptorEntity;
	}


	/**
	 * @return string
	 * @throws PackageDescriptorException
	 */
	public function getConfig(): string
	{
		if (!is_file(self::$configPackagePath)) {
			$this->createPackageConfig($this->getPackageDescriptorEntity());
		}

		return (string) file_get_contents(self::$configPackagePath);
	}


	/**
	 * @param string $packageName
	 * @return bool
	 * @throws PackageDescriptorCompileException
	 */
	public function isPackageInstalled(string $packageName): bool
	{
		foreach (self::$packageDescriptorEntity->getPackagest(false) as $package) {
			if ($package->getName() === $packageName) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param Configurator $configurator
	 */
	public function runAfterActions(Configurator $configurator): void
	{
		static $created = false;

		if ($created === false) {
			$created = true;
			if (self::$runAfterScripts === true) {
				$containerClass = $configurator->loadContainer();
				/** @var Container $container */
				$container = new $containerClass;

				foreach (self::$packageDescriptorEntity->getAfterInstallScripts() as $script) {
					if (\class_exists($script) === true) {
						/** @var IAfterInstall $instance */
						$instance = new $script($container, $this);
						$instance->run();
					}
				}
			}
		}
	}


	/**
	 * @param PackageDescriptorEntity $packageDescriptorEntity
	 * @throws PackageDescriptorException
	 */
	private function createPackageConfig(PackageDescriptorEntity $packageDescriptorEntity): void
	{
		$neon = [];

		foreach ($packageDescriptorEntity->getPackagest(true) as $package) {
			foreach ($package->getConfig() as $param => $value) {
				if (isset(self::$neonNoUseParams[$param]) === false) {
					$neon[$param][] = [
						'name' => $package->getName(),
						'version' => $package->getVersion(),
						'data' => $value,
					];
				}
			}
		}

		$return = '';
		$anonymousServiceCounter = 0;

		foreach ($neon as $param => $values) {
			$return .= "\n" . $param . ':' . "\n\t";
			$tree = [];

			if ($param === 'services') {
				foreach ($values as $value) {
					foreach ($neonData = Neon::decode($value['data']['data']) as $treeKey => $treeValue) {
						if (is_int($treeKey) || (is_string($treeKey) && preg_match('/^-?\d+\z/', $treeKey))) {
							unset($neonData[$treeKey]);
							$neonData['helperKey_' . $anonymousServiceCounter] = $treeValue;
							$anonymousServiceCounter++;
						}
					}

					$tree = Helpers::recursiveMerge($tree, $neonData);
				}

				$treeNumbers = [];
				$treeOthers = [];
				foreach ($tree as $treeKey => $treeValue) {
					if (preg_match('/^helperKey_\d+$/', $treeKey)) {
						$treeNumbers[] = $treeValue;
					} else {
						$treeOthers[$treeKey] = $treeValue;
					}
				}

				ksort($treeOthers);

				usort($treeNumbers, function ($left, $right): int {
					$score = static function ($item): int {
						if (\is_string($item)) {
							return 1;
						}

						$array = [];
						$score = 0;
						if (\is_iterable($item)) {
							$score = 2;
						}

						if ($item instanceof Entity) {
							$array = (array) $item->value;
							$score += 3;
						}

						if (isset($array['factory'])) {
							return $score + 1;
						}

						return $score;
					};

					if (($a = $score($left)) > ($b = $score($right))) {
						return -1;
					}

					return $a === $b ? 0 : 1;
				});

				$return .= str_replace("\n", "\n\t", Neon::encode($treeOthers, Neon::BLOCK));
				$return .= str_replace("\n", "\n\t", Neon::encode($treeNumbers, Neon::BLOCK));
				$tree = [];
			} else {
				foreach ($values as $value) {
					if ((bool) $value['data']['rewrite'] === false) {
						$return .= '# ' . $value['name'] . ($value['version'] ? ' (' . $value['version'] . ')' : '')
							. "\n\t" . str_replace("\n", "\n\t", $value['data']['data']);
					}

					if ((bool) $value['data']['rewrite'] === true) {
						$tree = Helpers::recursiveMerge($tree, $value['data']['data']);
					}
				}
			}

			if ($tree !== []) {
				$return .= str_replace("\n", "\n\t", Neon::encode($tree, Neon::BLOCK));
			}

			$return = trim($return) . "\n";
		}

		$return = (string) preg_replace('/(\s)\[\]\-(\s)/', '$1-$2', $return);

		if (!@file_put_contents(self::$configPackagePath, trim($return) . "\n")) {
			PackageDescriptorException::canNotRewritePackageNeon(self::$configPackagePath);
		}
	}


	/**
	 * @param PackageDescriptorEntity $packageDescriptorEntity
	 * @return bool
	 */
	private function isCacheExpired(PackageDescriptorEntity $packageDescriptorEntity): bool
	{
		if (!is_file(self::$configPackagePath) || !is_file(self::$configLocalPath)) {
			return true;
		}

		if ($packageDescriptorEntity->getComposerHash() !== $this->getComposerHash()) {
			return true;
		}

		return false;
	}


	/**
	 * Return hash of installed.json, if composer does not used, return empty string
	 *
	 * @return string
	 */
	private function getComposerHash(): string
	{
		static $cache;

		if ($cache === null) {
			$cache = (@md5_file(self::$projectRoot . '/vendor/composer/installed.json')) ?: md5((string) time());
		}

		return $cache;
	}
}
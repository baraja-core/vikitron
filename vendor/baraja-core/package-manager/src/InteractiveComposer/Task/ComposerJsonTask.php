<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;
use Baraja\PackageManager\Helpers;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;

class ComposerJsonTask extends BaseTask
{

	private const PRIORITY_PREFIX = 'baraja-';

	/**
	 * @return bool
	 * @throws JsonException
	 * @throws PackageDescriptorCompileException
	 */
	public function run(): bool
	{
		$path = $this->packageRegistrator->getProjectRoot() . '/composer.json';

		if (is_file($path) === false) {
			Helpers::terminalRenderError('Project composer.json does not exist! ' . $path);

			return false;
		}

		try {
			$composer = Json::decode(FileSystem::read($path), Json::FORCE_ARRAY);
		} catch (JsonException $e) {
			$composer = [];
		}

		// fix
		$require = $composer['require'] ?? [];

		foreach ($this->getPackageExtensions() as $ext) {
			if (isset($require[$ext]) === false) {
				$require[$ext] = '*';
			}
		}

		foreach ($require as $dependency => $version) {
			if (Strings::startsWith($dependency, self::PRIORITY_PREFIX)
				&& preg_match('/^\D+(?<mainVersion>\d+)\./', $version, $versionParser)
			) {
				$require[$dependency] = '~' . $versionParser['mainVersion'] . '.0';
			}
		}

		// build
		$composer['require'] = $require;

		FileSystem::write($path, preg_replace_callback('/\n(\s+)/', function (array $match): string {
			return "\n" . str_replace('    ', "\t", $match[1]);
		}, Json::encode($composer, Json::PRETTY)));

		return true;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return 'Composer.json fixer';
	}

	/**
	 * @return string[]
	 * @throws JsonException|PackageDescriptorCompileException
	 */
	private function getPackageExtensions(): array
	{
		$return = [];

		$packages = $this->packageRegistrator->getPackageDescriptorEntity()
			->getPackagest(false);

		foreach ($packages as $package) {
			$path = $this->packageRegistrator->getProjectRoot() . '/vendor/' . $package->getName() . '/composer.json';
			$composer = is_file($path) ? Json::decode(FileSystem::read($path), Json::FORCE_ARRAY) : [];
			if (isset($composer['require'])) {
				foreach ($composer['require'] as $dependency => $version) {
					if (isset($return[$dependency]) === false && Strings::startsWith($dependency, 'ext-')) {
						$return[$dependency] = true;
					}
				}
			}
		}

		return array_keys($return);
	}

}
<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorCompileException;

/**
 * @internal
 */
class PackageDescriptorEntity
{

	/** @var bool */
	protected $__close = false;

	/** @var \stdClass[] */
	protected $composer;

	/** @var mixed[] */
	protected $packagest = [];


	public function isClose(): bool
	{
		return $this->__close;
	}


	public function setClose(): void
	{
		$this->__close = true;
	}


	public function checkIfClose(): void
	{
		if ($this->isClose() === true) {
			throw new \RuntimeException('Package descriptor was closed to insert. Setters can be used only in compile time.');
		}
	}


	/**
	 * @return \stdClass[]
	 */
	public function getComposer(): array
	{
		return $this->composer;
	}


	/**
	 * @param \stdClass[] $composer
	 */
	public function setComposer(array $composer): void
	{
		$this->checkIfClose();
		$this->composer = $composer;
	}


	/**
	 * @param bool|null $customPackagesOnly
	 * @return Package[]
	 * @throws PackageDescriptorCompileException
	 */
	public function getPackagest(?bool $customPackagesOnly = null): array
	{
		$return = [];

		foreach ($this->packagest as $package) {
			if ($package['composer'] === null) {
				PackageDescriptorCompileException::composerJsonIsBroken($package['name']);
			}

			$return[] = new Package(
				$package['name'],
				$package['version'],
				$package['dependency'],
				$package['config'] ?? [],
				$package['composer']
			);
		}

		return $return;
	}


	/**
	 * @param mixed[] $packagest
	 */
	public function setPackages(array $packagest): void
	{
		$this->checkIfClose();

		$return = [];

		foreach ($packagest as $package) {
			$composer = [];
			if (isset($package['composer']) === true) {
				$composer = [
					'name' => $package['composer']['name'] ?? null,
					'description' => $package['composer']['description'] ?? null,
				];
			}

			$return[] = [
				'name' => $package['name'] ?? null,
				'version' => $package['version'] ?? null,
				'dependency' => $package['dependency'] ?? null,
				'config' => $package['config'] ?? null,
				'composer' => $composer,
			];
		}

		$this->packagest = $return;
	}


	public function getGeneratedDate(): string
	{
		return date('Y-m-d H:i:s');
	}


	public function getGeneratedDateTimestamp(): int
	{
		return time();
	}


	public function getComposerHash(): string
	{
		return md5((string) time());
	}
}

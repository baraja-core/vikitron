<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Nette\Neon\Neon;
use Nette\SmartObject;

final class Package
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var null|string
	 */
	private $version;

	/**
	 * @var string
	 */
	private $dependency;

	/**
	 * @var string[]
	 */
	private $config;

	/**
	 * @var mixed[]
	 */
	private $composer;

	/**
	 * @param string $name
	 * @param string|null $version
	 * @param string $dependency
	 * @param string[] $config
	 * @param mixed[] $composer
	 */
	public function __construct(string $name, ?string $version, string $dependency, array $config, array $composer)
	{
		$this->name = $name;
		$this->version = $version;
		$this->dependency = $dependency;
		$this->config = $config;
		$this->composer = $composer;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return null|string
	 */
	public function getVersion(): ?string
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getDependency(): string
	{
		return $this->dependency;
	}

	/**
	 * @return string[]
	 */
	public function getConfig(): array
	{
		return $this->config;
	}

	/**
	 * @return mixed[]
	 */
	public function getComposer(): array
	{
		return $this->composer;
	}

	/**
	 * @deprecated please use native implementation or DIC.
	 * @param string $key
	 * @return string[][]
	 */
	public function getConfigProperty(string $key): array
	{
		if (isset($this->getConfig()[$key])) {
			if ($this->getConfig()[$key]['rewrite'] === true) {
				return $this->getConfig()[$key]['data'];
			}

			return Neon::decode($this->getConfig()[$key]['data']);
		}

		return [];
	}

}
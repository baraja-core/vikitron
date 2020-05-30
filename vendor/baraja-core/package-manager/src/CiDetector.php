<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


use Baraja\PackageManager\Exception\PackageDescriptorException;

/**
 * @see https://github.com/OndraM/ci-detector
 */
final class CiDetector
{
	public const CI_APPVEYOR = 'AppVeyor';

	public const CI_BAMBOO = 'Bamboo';

	public const CI_BUDDY = 'Buddy';

	public const CI_CIRCLE = 'CircleCI';

	public const CI_CODESHIP = 'Codeship';

	public const CI_CONTINUOUSPHP = 'continuousphp';

	public const CI_DRONE = 'drone';

	public const CI_GITHUB_ACTIONS = 'GitHub Actions';

	public const CI_GITLAB = 'GitLab';

	public const CI_JENKINS = 'Jenkins';

	public const CI_TEAMCITY = 'TeamCity';

	public const CI_TRAVIS = 'Travis CI';

	/** @var Env */
	private $environment;


	public function __construct()
	{
		$this->environment = new Env;
	}


	/**
	 * Is current environment an recognized CI server?
	 */
	public function isCiDetected(): bool
	{
		return $this->detectCurrentCiServer() !== null;
	}


	/**
	 * Detect current CI server and return instance of its settings
	 *
	 * @throws PackageDescriptorException
	 */
	public function detect(): CiInterface
	{
		if (($ciServer = $this->detectCurrentCiServer()) === null) {
			throw new PackageDescriptorException('No CI server detected in current environment');
		}

		return $ciServer;
	}


	/**
	 * @return string[]
	 */
	private function getCiServers(): array
	{
		return [
			AppVeyor::class,
			Bamboo::class,
			Buddy::class,
			Circle::class,
			Codeship::class,
			Continuousphp::class,
			Drone::class,
			GitHubActions::class,
			GitLab::class,
			Jenkins::class,
			TeamCity::class,
			Travis::class,
		];
	}


	private function detectCurrentCiServer(): ?CiInterface
	{
		$ciServers = $this->getCiServers();

		foreach ($ciServers as $ciClass) {
			if (call_user_func([$ciClass, 'isDetected'], $this->environment)) {
				return new $ciClass($this->environment);
			}
		}

		return null;
	}
}
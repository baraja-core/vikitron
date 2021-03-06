<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


class GitHubActions extends AbstractCi
{
	public const GITHUB_BASE_URL = 'https://github.com';


	public static function isDetected(Env $env): bool
	{
		return $env->get('GITHUB_ACTIONS') !== false;
	}


	public function getCiName(): string
	{
		return CiDetector::CI_GITHUB_ACTIONS;
	}


	public function isPullRequest(): TrinaryLogic
	{
		return TrinaryLogic::createFromBoolean($this->env->getString('GITHUB_EVENT_NAME') === 'pull_request');
	}


	public function getBuildNumber(): string
	{
		return $this->env->getString('GITHUB_ACTION');
	}


	public function getBuildUrl(): string
	{
		return sprintf(
			'%s/%s/commit/%s/checks',
			self::GITHUB_BASE_URL,
			$this->env->get('GITHUB_REPOSITORY'),
			$this->env->get('GITHUB_SHA')
		);
	}


	public function getGitCommit(): string
	{
		return $this->env->getString('GITHUB_SHA');
	}


	public function getGitBranch(): string
	{
		$gitReference = $this->env->getString('GITHUB_REF');

		return preg_replace('~^refs/heads/~', '', $gitReference);
	}


	public function getRepositoryName(): string
	{
		return $this->env->getString('GITHUB_REPOSITORY');
	}


	public function getRepositoryUrl(): string
	{
		return sprintf(
			'%s/%s',
			self::GITHUB_BASE_URL,
			$this->env->get('GITHUB_REPOSITORY')
		);
	}
}

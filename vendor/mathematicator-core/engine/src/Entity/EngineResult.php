<?php

namespace Mathematicator\Engine;


use Nette\SmartObject;
use Nette\Utils\Strings;

class EngineResult
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var string|null
	 */
	private $matchedRoute;

	/**
	 * @var int
	 */
	private $time;

	/**
	 * @param string $query
	 * @param string|null $matchedRoute
	 */
	public function __construct(string $query, ?string $matchedRoute)
	{
		$this->query = $query;
		$this->matchedRoute = $matchedRoute;
	}

	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * @return int
	 */
	public function getLength(): int
	{
		return Strings::length($this->getQuery());
	}

	/**
	 * @return string|null
	 */
	public function getMatchedRoute(): ?string
	{
		return $this->matchedRoute;
	}

	/**
	 * @return int
	 */
	public function getTime(): int
	{
		return $this->time ?? 0;
	}

	/**
	 * @param int $time
	 */
	public function setTime(int $time): void
	{
		$this->time = $time;
	}

}
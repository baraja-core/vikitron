<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Entity;


use Nette\SmartObject;
use Nette\Utils\Strings;

abstract class EngineResult
{
	use SmartObject;

	/** @var string */
	private $query;

	/** @var string|null */
	private $matchedRoute;

	/** @var int */
	private $time;

	/** @var float */
	private $startTime;


	public function __construct(string $query, ?string $matchedRoute)
	{
		$this->query = $query;
		$this->matchedRoute = $matchedRoute;
		$this->startTime = (float) microtime(true);
	}


	final public function getQuery(): string
	{
		return $this->query;
	}


	final public function getLength(): int
	{
		return Strings::length($this->getQuery());
	}


	final public function getMatchedRoute(): ?string
	{
		return $this->matchedRoute;
	}


	/**
	 * Return processing time in milliseconds.
	 */
	final public function getTime(): float
	{
		return (microtime(true) - $this->startTime) * 1000;
	}
}

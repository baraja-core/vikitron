<?php

namespace Mathematicator\Search;

use Nette\SmartObject;

/**
 * @property float $time
 * @property string $query
 * @property int $length
 * @property int $userRequests
 * @property Box $interpret
 * @property string $matchedRoute
 * @property Box[] $boxes
 */
class Result
{

	use SmartObject;

	/**
	 * @var float
	 */
	private $time;

	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var int
	 */
	private $length = 0;

	/**
	 * @var int
	 */
	private $userRequests = 0;

	/**
	 * @var Box
	 */
	private $interpret;

	/**
	 * @var string
	 */
	private $matchedRoute;

	/**
	 * @var Box[]
	 */
	private $boxes;

	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * @param string $query
	 */
	public function setQuery(string $query): void
	{
		$this->query = $query;
	}

	/**
	 * @return int
	 */
	public function getLength(): int
	{
		return $this->length;
	}

	/**
	 * @param int $length
	 */
	public function setLength(int $length): void
	{
		$this->length = $length;
	}

	/**
	 * @return int
	 */
	public function getUserRequests(): int
	{
		return $this->userRequests;
	}

	/**
	 * @param int $userRequests
	 */
	public function setUserRequests(int $userRequests): void
	{
		$this->userRequests = $userRequests;
	}

	/**
	 * @return null|Box
	 */
	public function getInterpret(): ?Box
	{
		return $this->interpret;
	}

	/**
	 * @param Box $interpret
	 */
	public function setInterpret(Box $interpret): void
	{
		$this->interpret = $interpret;
	}

	/**
	 * @return string
	 */
	public function getMatchedRoute(): string
	{
		return $this->matchedRoute;
	}

	/**
	 * @param string $matchedRoute
	 */
	public function setMatchedRoute(string $matchedRoute): void
	{
		$this->matchedRoute = $matchedRoute;
	}

	/**
	 * @return Box[]
	 */
	public function getBoxes(): array
	{
		return $this->boxes;
	}

	/**
	 * @param Box[] $boxes
	 */
	public function setBoxes(array $boxes): void
	{
		$this->boxes = $boxes;
	}

	/**
	 * @return float
	 */
	public function getTime(): float
	{
		return $this->time;
	}

	/**
	 * @param float $time
	 */
	public function setTime(float $time): void
	{
		$this->time = $time;
	}

}

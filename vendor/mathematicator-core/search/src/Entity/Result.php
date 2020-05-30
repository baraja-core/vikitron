<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Mathematicator\Engine\Box;
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
final class Result
{
	use SmartObject;

	/** @var float */
	private $time;

	/** @var string */
	private $query;

	/** @var int */
	private $length = 0;

	/** @var int */
	private $userRequests = 0;

	/** @var Box */
	private $interpret;

	/** @var string */
	private $matchedRoute;

	/** @var Box[] */
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
	 * @return Result
	 */
	public function setQuery(string $query): self
	{
		$this->query = $query;

		return $this;
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
	 * @return Result
	 */
	public function setLength(int $length): self
	{
		$this->length = $length;

		return $this;
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
	 * @return Result
	 */
	public function setUserRequests(int $userRequests): self
	{
		$this->userRequests = $userRequests;

		return $this;
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
	 * @return Result
	 */
	public function setInterpret(Box $interpret): self
	{
		$this->interpret = $interpret;

		return $this;
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
	 * @return Result
	 */
	public function setMatchedRoute(string $matchedRoute): self
	{
		$this->matchedRoute = $matchedRoute;

		return $this;
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
	 * @return Result
	 */
	public function setBoxes(array $boxes): self
	{
		$this->boxes = $boxes;

		return $this;
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
	 * @return Result
	 */
	public function setTime(float $time): self
	{
		$this->time = $time;

		return $this;
	}
}

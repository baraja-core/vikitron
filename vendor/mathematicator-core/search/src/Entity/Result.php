<?php

declare(strict_types=1);

namespace Mathematicator\Search\Entity;


use Mathematicator\Engine\Entity\Box;
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


	public function getQuery(): string
	{
		return $this->query;
	}


	public function setQuery(string $query): self
	{
		$this->query = $query;

		return $this;
	}


	public function getLength(): int
	{
		return $this->length;
	}


	public function setLength(int $length): self
	{
		$this->length = $length;

		return $this;
	}


	public function getUserRequests(): int
	{
		return $this->userRequests;
	}


	public function setUserRequests(int $userRequests): self
	{
		$this->userRequests = $userRequests;

		return $this;
	}


	public function getInterpret(): ?Box
	{
		return $this->interpret;
	}


	public function setInterpret(Box $interpret): self
	{
		$this->interpret = $interpret;

		return $this;
	}


	public function getMatchedRoute(): string
	{
		return $this->matchedRoute;
	}


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


	public function getTime(): float
	{
		return $this->time;
	}


	public function setTime(float $time): self
	{
		$this->time = $time;

		return $this;
	}
}

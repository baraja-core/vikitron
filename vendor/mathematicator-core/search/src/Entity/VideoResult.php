<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Nette\SmartObject;

/**
 * @property string $name
 * @property string $link
 * @property string $thumbnail
 * @property string $description
 * @property float $score
 */
class VideoResult
{
	use SmartObject;

	/** @var string */
	private $name;

	/** @var string */
	private $link;

	/** @var string */
	private $thumbnail;

	/** @var string */
	private $description;

	/** @var float */
	private $score = 0;


	/**
	 * @return string
	 */
	public function getLink(): string
	{
		return $this->link;
	}


	/**
	 * @param string $link
	 * @return VideoResult
	 */
	public function setLink(string $link): self
	{
		$this->link = $link;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getThumbnail(): string
	{
		return $this->thumbnail;
	}


	/**
	 * @param string $thumbnail
	 * @return VideoResult
	 */
	public function setThumbnail(string $thumbnail): self
	{
		$this->thumbnail = $thumbnail;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}


	/**
	 * @param string|null $description
	 * @return VideoResult
	 */
	public function setDescription(string $description = null): self
	{
		$this->description = $description ?? '';

		return $this;
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}


	/**
	 * @param string|null $name
	 * @return VideoResult
	 */
	public function setName(string $name = null): self
	{
		$this->name = $name ?? '';

		return $this;
	}


	/**
	 * @return float
	 */
	public function getScore(): float
	{
		return $this->score;
	}


	/**
	 * @param float $score
	 * @return VideoResult
	 */
	public function setScore(float $score): self
	{
		$this->score = $score;

		return $this;
	}
}

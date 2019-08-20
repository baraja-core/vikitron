<?php

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

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $link;

	/**
	 * @var string
	 */
	private $thumbnail;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var float
	 */
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
	 */
	public function setLink(string $link)
	{
		$this->link = $link;
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
	 */
	public function setThumbnail(string $thumbnail)
	{
		$this->thumbnail = $thumbnail;
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
	 */
	public function setDescription(string $description = null)
	{
		$this->description = $description === null ? '' : $description;
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
	 */
	public function setName(string $name = null)
	{
		$this->name = $name === null ? '' : $name;
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
	 */
	public function setScore(float $score)
	{
		$this->score = $score;
	}

}

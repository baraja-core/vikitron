<?php

declare(strict_types=1);

namespace Mathematicator\Search\Entity;


use Nette\SmartObject;

/**
 * @property Result $result
 * @property VideoResult[] $videos
 */
class AutoCompleteResult
{
	use SmartObject;

	/** @var Result */
	private $result;


	public function getResult(): Result
	{
		return $this->result;
	}


	public function setResult(Result $result): self
	{
		$this->result = $result;

		return $this;
	}


	/**
	 * @return VideoResult[]
	 */
	public function getVideos(): array
	{
		return $this->videos;
	}


	/**
	 * @param VideoResult[] $videos
	 * @return self
	 */
	public function setVideos(array $videos): self
	{
		$this->videos = $videos;

		return $this;
	}
}

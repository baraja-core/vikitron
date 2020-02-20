<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Nette\SmartObject;

/**
 * @property Result $result
 * @property VideoResult[] $videos
 */
class AutoCompleteResult
{

	use SmartObject;

	/**
	 * @var Result
	 */
	private $result;

	/**
	 * @return Result
	 */
	public function getResult(): Result
	{
		return $this->result;
	}

	/**
	 * @param Result $result
	 * @return AutoCompleteResult
	 */
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
	 * @return AutoCompleteResult
	 */
	public function setVideos(array $videos): self
	{
		$this->videos = $videos;

		return $this;
	}

}

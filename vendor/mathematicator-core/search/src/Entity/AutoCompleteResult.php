<?php

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
	 */
	public function setResult(Result $result)
	{
		$this->result = $result;
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
	 */
	public function setVideos(array $videos)
	{
		$this->videos = $videos;
	}

}

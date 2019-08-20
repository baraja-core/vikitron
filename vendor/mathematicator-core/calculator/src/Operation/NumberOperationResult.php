<?php

namespace Mathematicator\Calculator\Operation;

use Mathematicator\Tokenizer\Token\NumberToken;
use Nette\SmartObject;

/**
 * @property NumberToken $number
 * @property null|string $title
 * @property null|string $description
 * @property null|string $ajaxEndpoint
 */
class NumberOperationResult
{

	use SmartObject;

	/**
	 * @var NumberToken
	 */
	private $number;

	/**
	 * @var null|string
	 */
	private $title;

	/**
	 * @var null|string
	 */
	private $description;

	/**
	 * @var null|string
	 */
	private $ajaxEndpoint;

	/**
	 * @var int
	 */
	private $iteratorStep = 2;

	/**
	 * @return NumberToken
	 */
	public function getNumber(): NumberToken
	{
		return $this->number;
	}

	/**
	 * @param NumberToken $number
	 */
	public function setNumber(NumberToken $number): void
	{
		$this->number = $number;
	}

	/**
	 * @return null|string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return null|string
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * @param string|null $title
	 */
	public function setTitle(string $title = null): void
	{
		$this->title = $title;
	}

	/**
	 * @return null|string
	 */
	public function getAjaxEndpoint(): ?string
	{
		return $this->ajaxEndpoint;
	}

	/**
	 * @param string|null $ajaxEndpoint
	 */
	public function setAjaxEndpoint(string $ajaxEndpoint = null): void
	{
		$this->ajaxEndpoint = $ajaxEndpoint;
	}

	/**
	 * @return int
	 */
	public function getIteratorStep(): int
	{
		return $this->iteratorStep;
	}

	/**
	 * @param int $iteratorStep
	 */
	public function setIteratorStep(int $iteratorStep): void
	{
		$this->iteratorStep = $iteratorStep;
	}

}

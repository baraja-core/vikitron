<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Operation;


use Mathematicator\Tokenizer\Token\NumberToken;
use Nette\SmartObject;

/**
 * @property NumberToken $number
 * @property string|null $title
 * @property string|null $description
 * @property string|null $ajaxEndpoint
 */
class NumberOperationResult
{
	use SmartObject;

	/** @var NumberToken */
	private $number;

	/** @var string|null */
	private $title;

	/** @var string|null */
	private $description;

	/** @var string|null */
	private $ajaxEndpoint;

	/** @var int */
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
	 * @return NumberOperationResult
	 */
	public function setNumber(NumberToken $number): self
	{
		$this->number = $number;

		return $this;
	}


	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}


	/**
	 * @param string $description
	 * @return NumberOperationResult
	 */
	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}


	/**
	 * @return string|null
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}


	/**
	 * @param string|null $title
	 * @return NumberOperationResult
	 */
	public function setTitle(string $title = null): self
	{
		$this->title = $title;

		return $this;
	}


	/**
	 * @return string|null
	 */
	public function getAjaxEndpoint(): ?string
	{
		return $this->ajaxEndpoint;
	}


	/**
	 * @param string|null $ajaxEndpoint
	 * @return NumberOperationResult
	 */
	public function setAjaxEndpoint(string $ajaxEndpoint = null): self
	{
		$this->ajaxEndpoint = $ajaxEndpoint;

		return $this;
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
	 * @return NumberOperationResult
	 */
	public function setIteratorStep(int $iteratorStep): self
	{
		$this->iteratorStep = $iteratorStep;

		return $this;
	}
}

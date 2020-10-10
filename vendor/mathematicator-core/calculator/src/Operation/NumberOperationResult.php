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


	public function getNumber(): NumberToken
	{
		return $this->number;
	}


	public function setNumber(NumberToken $number): self
	{
		$this->number = $number;

		return $this;
	}


	public function getDescription(): ?string
	{
		return $this->description;
	}


	public function setDescription(string $description): self
	{
		$this->description = $description;

		return $this;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function setTitle(string $title = null): self
	{
		$this->title = $title;

		return $this;
	}


	public function getAjaxEndpoint(): ?string
	{
		return $this->ajaxEndpoint;
	}


	public function setAjaxEndpoint(string $ajaxEndpoint = null): self
	{
		$this->ajaxEndpoint = $ajaxEndpoint;

		return $this;
	}


	public function getIteratorStep(): int
	{
		return $this->iteratorStep;
	}


	public function setIteratorStep(int $iteratorStep): self
	{
		$this->iteratorStep = $iteratorStep;

		return $this;
	}
}

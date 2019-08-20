<?php

namespace Mathematicator\Calculator;

use Mathematicator\Tokenizer\Token\BaseToken;
use Nette\SmartObject;

/**
 * @property BaseToken[] $result
 * @property string $stepTitle
 * @property string $stepDescription
 * @property bool $wasModified
 * @property null|string $ajaxEndpoint
 */
class TokensCalculatorResult
{

	use SmartObject;

	/**
	 * @var BaseToken[]
	 */
	private $result;

	/**
	 * @var null|string
	 */
	private $stepTitle;

	/**
	 * @var null|string
	 */
	private $stepDescription;

	/**
	 * @var bool
	 */
	private $wasModified = false;

	/**
	 * @var null|string
	 */
	private $ajaxEndpoint;

	/**
	 * @return BaseToken[]
	 */
	public function getResult(): array
	{
		return $this->result;
	}

	/**
	 * @param BaseToken[] $result
	 */
	public function setResult(array $result): void
	{
		$this->result = $result;
	}

	/**
	 * @return null|string
	 */
	public function getStepTitle(): ?string
	{
		return $this->stepTitle;
	}

	/**
	 * @param null|string $stepTitle
	 */
	public function setStepTitle(string $stepTitle = null): void
	{
		$this->stepTitle = $stepTitle;
	}

	/**
	 * @return null|string
	 */
	public function getStepDescription(): ?string
	{
		return $this->stepDescription;
	}

	/**
	 * @param string|null $stepDescription
	 */
	public function setStepDescription(string $stepDescription = null): void
	{
		$this->stepDescription = $stepDescription;
	}

	/**
	 * @return bool
	 */
	public function getWasModified(): bool
	{
		return $this->wasModified;
	}

	/**
	 * @param bool $wasModified
	 */
	public function setWasModified(bool $wasModified): void
	{
		$this->wasModified = $wasModified;
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

}

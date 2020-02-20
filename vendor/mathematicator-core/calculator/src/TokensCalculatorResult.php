<?php

declare(strict_types=1);

namespace Mathematicator\Calculator;


use Mathematicator\Tokenizer\Token\BaseToken;
use Nette\SmartObject;

/**
 * @property BaseToken[] $result
 * @property string|null $stepTitle
 * @property string|null $stepDescription
 * @property bool $wasModified
 * @property string|null $ajaxEndpoint
 */
class TokensCalculatorResult
{

	use SmartObject;

	/**
	 * @var BaseToken[]
	 */
	private $result;

	/**
	 * @var string|null
	 */
	private $stepTitle;

	/**
	 * @var string|null
	 */
	private $stepDescription;

	/**
	 * @var bool
	 */
	private $wasModified = false;

	/**
	 * @var string|null
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
	 * @return TokensCalculatorResult
	 */
	public function setResult(array $result): self
	{
		$this->result = $result;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getStepTitle(): ?string
	{
		return $this->stepTitle;
	}

	/**
	 * @param string|null $stepTitle
	 * @return TokensCalculatorResult
	 */
	public function setStepTitle(string $stepTitle = null): self
	{
		$this->stepTitle = $stepTitle;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getStepDescription(): ?string
	{
		return $this->stepDescription;
	}

	/**
	 * @param string|null $stepDescription
	 * @return TokensCalculatorResult
	 */
	public function setStepDescription(string $stepDescription = null): self
	{
		$this->stepDescription = $stepDescription;

		return $this;
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
	 * @return TokensCalculatorResult
	 */
	public function setWasModified(bool $wasModified): self
	{
		$this->wasModified = $wasModified;

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
	 * @return TokensCalculatorResult
	 */
	public function setAjaxEndpoint(string $ajaxEndpoint = null): self
	{
		$this->ajaxEndpoint = $ajaxEndpoint;

		return $this;
	}

}

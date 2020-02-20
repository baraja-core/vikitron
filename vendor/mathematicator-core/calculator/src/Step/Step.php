<?php

declare(strict_types=1);

namespace Mathematicator\Calculator;


use Nette\SmartObject;

class Step
{

	use SmartObject;

	/**
	 * @var string|null
	 */
	private $title;

	/**
	 * @var bool
	 */
	private $htmlTitle = false;

	/**
	 * @var string|null
	 */
	private $latex;

	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * @var bool
	 */
	private $htmlDescription = false;

	/**
	 * @var string|null
	 */
	private $ajaxEndpoint;

	/**
	 * @param string|null $title
	 * @param string|null $latex
	 * @param string|null $description
	 */
	public function __construct(?string $title, ?string $latex, ?string $description = null)
	{
		$this->title = $title;
		$this->latex = $latex;
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
	 * @param bool $html
	 */
	public function setTitle(string $title = null, bool $html = false): void
	{
		$this->title = $title;
		$this->htmlTitle = $html;
	}

	/**
	 * @return bool
	 */
	public function isHtmlTitle(): bool
	{
		return $this->htmlTitle;
	}

	/**
	 * @return string|null
	 */
	public function getLatex(): ?string
	{
		return $this->latex;
	}

	/**
	 * @param string|null $latex
	 */
	public function setLatex(?string $latex = null): void
	{
		$this->latex = $latex ? : null;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 * @param bool $html
	 */
	public function setDescription(?string $description = null, bool $html = false): void
	{
		$this->description = $description;
		$this->htmlDescription = $html;
	}

	/**
	 * @return bool
	 */
	public function isHtmlDescription(): bool
	{
		return $this->htmlDescription;
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
	 */
	public function setAjaxEndpoint(string $ajaxEndpoint = null): void
	{
		$this->ajaxEndpoint = $ajaxEndpoint;
	}

}

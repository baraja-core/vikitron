<?php

namespace Mathematicator\Calculator;

use Nette\SmartObject;


class Step
{

	use SmartObject;

	/**
	 * @var null|string
	 */
	private $title;

	/**
	 * @var bool
	 */
	private $htmlTitle = false;

	/**
	 * @var null|string
	 */
	private $latex;

	/**
	 * @var null|string
	 */
	private $description;

	/**
	 * @var bool
	 */
	private $htmlDescription = false;

	/**
	 * @var null|string
	 */
	private $ajaxEndpoint;

	/**
	 * @param null|string $title
	 * @param null|string $latex
	 * @param null|string $description
	 */
	public function __construct(?string $title, ?string $latex, ?string $description)
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
	 * @param null|string $title
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
	 * @return null|string
	 */
	public function getLatex(): ?string
	{
		return $this->latex;
	}

	/**
	 * @param null|string $latex
	 */
	public function setLatex(string $latex = null): void
	{
		$this->latex = $latex;
	}

	/**
	 * @return null|string
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 * @param bool $html
	 */
	public function setDescription(string $description = null, bool $html = false): void
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
	 * @return null|string
	 */
	public function getAjaxEndpoint(): ?string
	{
		return $this->ajaxEndpoint;
	}

	/**
	 * @param null|string $ajaxEndpoint
	 */
	public function setAjaxEndpoint(string $ajaxEndpoint = null): void
	{
		$this->ajaxEndpoint = $ajaxEndpoint;
	}

}

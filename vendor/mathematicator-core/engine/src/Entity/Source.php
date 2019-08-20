<?php

namespace Mathematicator\Engine;


use Nette\SmartObject;
use Nette\Utils\Validators;

class Source
{

	use SmartObject;

	/**
	 * @var string|null
	 */
	private $title;

	/**
	 * @var \string[]
	 */
	private $authors = [];

	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * @var string|null
	 */
	private $url;

	/**
	 * @param string|null $title
	 * @param string|null $url
	 * @param string|null $description
	 */
	public function __construct(?string $title = null, ?string $url = null, ?string $description = null)
	{
		$this->title = $title;
		$this->url = $url;
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		$title = null;
		$description = null;

		if ($this->title) {
			$title = '<b>' . $this->title . '</b><br>';
		}

		if ($this->description) {
			$description = $this->description
				. ($this->authors !== []
					? '<br><br><span class="text-secondary">- ' . implode('<br>- ', $this->authors) . '</span>'
					: ''
				);
		}

		if ($this->url !== null && Validators::isUrl($this->url)) {
			return '<a href="' . $this->url . '" target="_blank">'
				. ($title ?? $this->url)
				. '</a>'
				. ($description ?? '');
		}

		return $title === null && $description === null
			? ''
			: '<b>' . $title . '</b><br>' . $description;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor(string $author): void
	{
		$this->authors[] = $author;
	}

	/**
	 * @param \string[] $authors
	 */
	public function setAuthors(array $authors): void
	{
		if ($authors !== []) {
			foreach ($authors as $author) {
				$this->authors[] = $author;
			}
		}
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

}
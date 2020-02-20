<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Nette\SmartObject;

class DynamicConfiguration
{

	use SmartObject;

	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var string|null
	 */
	private $title;

	/**
	 * @var string[]|null[]
	 */
	private $data = [];

	/**
	 * @var string[]
	 */
	private $defaults = [];

	/**
	 * @var string[]
	 */
	private $labels = [];

	/**
	 * @param string $key
	 */
	public function __construct(string $key)
	{
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getKey(): string
	{
		return $this->key;
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
	 * @return DynamicConfiguration
	 */
	public function setTitle(?string $title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSerialized(): string
	{
		$return = '';

		foreach ($this->data as $key => $value) {
			if (($value = trim((string) $value)) !== '') {
				$return .= ($return ? '&' : '') . urlencode($key) . '=' . urlencode($value);
			}
		}

		return $return;
	}

	/**
	 * @param string $key
	 * @param string|null $default
	 * @return string|null
	 */
	public function getValue(string $key, ?string $default = null): ?string
	{
		$this->defaults[$key] = $default;

		return $this->data[$key] ?? $default;
	}

	/**
	 * @return string[]|null[]
	 */
	public function getValues(): array
	{
		$return = $this->data;

		foreach ($this->defaults as $key => $value) {
			if ($value !== null || isset($this->data[$key]) === false) {
				$return[$key] = $value;
			}
		}

		return $return;
	}

	/**
	 * @return string[]
	 */
	public function getLabels(): array
	{
		return $this->labels;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public function getLabel(string $key): string
	{
		return $this->labels[$key] ?? $key;
	}

	/**
	 * @param string $key
	 * @param string|null $label
	 * @return DynamicConfiguration
	 */
	public function addLabel(string $key, ?string $label): self
	{
		if ($label === null && isset($this->labels[$key])) {
			unset($this->labels[$key]);
		}

		if ($label !== null) {
			$this->labels[$key] = $label;
		}

		return $this;
	}

	/**
	 * @param array $haystack
	 * @return DynamicConfiguration
	 */
	public function setValues(array $haystack): self
	{
		foreach ($haystack as $key => $value) {
			if (isset($this->data[$key]) === false) {
				$this->data[$key] = ((string) $value) ? : null;
			}
		}

		return $this;
	}

	/**
	 * @param string $key
	 * @param string|null $value
	 * @return DynamicConfiguration
	 */
	public function setValue(string $key, ?string $value = null): self
	{
		$this->data[$key] = $value;

		return $this;
	}

}
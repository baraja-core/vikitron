<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Entity;


use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

final class Query
{
	use SmartObject;

	public const LENGTH_LIMIT = 1024;

	/** @var string */
	private $original;

	/** @var string */
	private $query;

	/** @var string */
	private $locale = 'cs';

	/** @var int */
	private $decimals = 8;

	/** @var bool[] (haystack => true) */
	private $filteredTags = [];

	/** @var float */
	private $latitude = 50.0755381;

	/** @var float */
	private $longitude = 14.4378005;

	/** @var \DateTime */
	private $dateTime;


	public function __construct(string $original, string $query)
	{
		$this->original = $original;
		$this->query = $this->process($query);
		$this->dateTime = DateTime::from('now');
	}


	public function __toString(): string
	{
		return $this->query;
	}


	public function getOriginal(): string
	{
		return $this->original;
	}


	public function getQuery(): string
	{
		return $this->query;
	}


	public function getLocale(): string
	{
		return $this->locale;
	}


	public function getDecimals(): int
	{
		return $this->decimals;
	}


	public function isDefaultDecimals(): bool
	{
		return $this->decimals === 8;
	}


	public function getLatitude(): float
	{
		return $this->latitude;
	}


	public function getLongitude(): float
	{
		return $this->longitude;
	}


	public function getDateTime(): \DateTime
	{
		return $this->dateTime;
	}


	/**
	 * @return string[]
	 */
	public function getFilteredTags(): array
	{
		return array_keys($this->filteredTags);
	}


	private function process(string $query): string
	{
		$query = (string) preg_replace_callback(
			'/\s+na\s+(\d+)\s+(?:míst[oay]?)|\s+to\s+(\d+)\s+digits?/u',
			function (array $match): string {
				$this->decimals = (int) ($match[1] ?: $match[2]);

				return '';
			}, $query
		);

		$filters = $this->processFilterTags(strtolower(Strings::toAscii($query)));

		return $this->filteredTags === [] ? $query : $filters;
	}


	private function processFilterTags(string $query): string
	{
		static $patterns = [
			'^delitele?\s*(cisla\s*)?\s*(?<query>\d+)$' => ['divisors'],
			'^(prvociselny\s+)?rozklad?\s*(cisla\s*)?\s*(?<query>\d+)$' => ['prime-factorization'],
		];

		foreach ($patterns as $pattern => $tags) {
			if (preg_match('/' . $pattern . '/', $query, $parser)) {
				$query = $parser['query'] ?? $query;
				foreach ($tags as $tag) {
					$this->filteredTags[$tag] = true;
				}
			}
		}

		return $query;
	}
}

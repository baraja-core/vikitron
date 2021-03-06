<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Entity;


use function count;
use Mathematicator\Engine\Exception\TerminateException;
use Mathematicator\Engine\Helpers;
use Nette\SmartObject;

final class Context
{
	use SmartObject;

	public const BOXES_LIMIT = 100;

	/** @var string */
	private $query;

	/** @var Query */
	private $queryEntity;

	/** @var Box[] */
	private $boxes = [];

	/** @var Source[] */
	private $sources = [];

	/** @var DynamicConfiguration[] */
	private $dynamicConfigurations = [];

	/** @var Box|null */
	private $interpret;


	/**
	 * @param Query $query
	 */
	public function __construct(Query $query)
	{
		$this->query = $query->getQuery();
		$this->queryEntity = $query;
	}


	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}


	/**
	 * @return Query
	 */
	public function getQueryEntity(): Query
	{
		return $this->queryEntity;
	}


	/**
	 * @param string $type
	 * @return Box
	 * @throws TerminateException
	 */
	public function addBox(string $type): Box
	{
		if (count($this->boxes) >= self::BOXES_LIMIT) {
			throw new TerminateException(__METHOD__);
		}

		$this->boxes[] = ($box = new Box($type));

		return $box;
	}


	/**
	 * @return Box[]
	 */
	public function getBoxes(): array
	{
		return $this->boxes ?? [];
	}


	/**
	 * @return Source[]
	 */
	public function getSources(): array
	{
		return $this->sources;
	}


	/**
	 * @internal
	 */
	public function resetBoxes(): void
	{
		$this->boxes = [];
	}


	/**
	 * @return Box|null
	 */
	public function getInterpret(): ?Box
	{
		return $this->interpret;
	}


	/**
	 * @param string $boxType
	 * @param string|null $content
	 * @return Box
	 */
	public function setInterpret(string $boxType, ?string $content = null): Box
	{
		return $this->interpret = (new Box($boxType, 'Interpretace zadání dotazu', $content))
			->setIcon('fas fa-project-diagram');
	}


	/**
	 * @param Source $source
	 */
	public function addSource(Source $source): void
	{
		$this->sources[] = $source;
	}


	/**
	 * @param string $key
	 * @return DynamicConfiguration
	 */
	public function getDynamicConfiguration(string $key): DynamicConfiguration
	{
		if (isset($this->dynamicConfigurations[$key]) === false) {
			$this->dynamicConfigurations[$key] = new DynamicConfiguration($key);
		}

		return $this->dynamicConfigurations[$key];
	}


	/**
	 * @return DynamicConfiguration[]
	 */
	public function getDynamicConfigurations(): array
	{
		return $this->dynamicConfigurations;
	}


	/**
	 * Generate absolute URL to result page by given query.
	 * Route is defined by internal convention, in future it can be changed.
	 *
	 * @param string $query
	 * @return string
	 */
	public function link(string $query): string
	{
		return Helpers::getBaseUrl() . '/search' . (($query = trim($query)) !== '' ? '?q=' . urlencode($query) : '');
	}
}

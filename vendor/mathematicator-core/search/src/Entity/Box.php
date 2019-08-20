<?php

namespace Mathematicator\Search;

use Latte\Runtime\Filters;
use Mathematicator\Calculator\Step;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\Strings;


class Box
{

	use SmartObject;

	public const TYPE_UNDEFINED = 'type_undefined';
	public const TYPE_INTERPRET = 'type_interpret';
	public const TYPE_TEXT = 'type_text';
	public const TYPE_LATEX = 'type_latex';
	public const TYPE_HTML = 'type_html';
	public const TYPE_GRAPH = 'type_graph';
	public const TYPE_TABLE = 'type_table';

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $icon = '&#xE155';

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var string|null
	 */
	private $url;

	/**
	 * @var int
	 */
	private $rank;

	/**
	 * @var Step[]
	 */
	private $steps;

	/**
	 * @param string $type
	 * @param string|null $title
	 * @param string|null $text
	 * @param string|null $url
	 * @param int $rank
	 */
	public function __construct(
		string $type = self::TYPE_UNDEFINED,
		?string $title = null,
		?string $text = null,
		?string $url = null,
		int $rank = 32
	)
	{
		$this->type = $type;
		$this->title = $title;
		$this->text = Strings::normalize((string) $text);
		$this->url = $url === '' ? null : $url;
		$this->setRank($rank);
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->type === self::TYPE_TEXT
			? Filters::escapeHtmlText($this->text)
			: '';
	}

	/**
	 * @param int[]|string[] $table
	 */
	public function setTable(array $table): void
	{
		$this->text = Json::encode($table);
	}

	/**
	 * @param string[]|int[] $table
	 * @return Box
	 */
	public function setKeyValue(array $table = []): self
	{
		if ($table !== []) {
			$buffer = '';

			foreach ($table as $key => $value) {
				$buffer .= '<tr>'
					. '<th' . ($buffer === '' ? ' style="width:33%"' : '') . '>'
					. $key
					. ':</th>'
					. '<td>' . $value . '</td>'
					. '</tr>';
			}

			$this->text = '<table>' . $buffer . '</table>';
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getIcon(): string
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 * @return Box
	 */
	public function setIcon(string $icon): self
	{
		$this->icon = $icon;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 * @return Box
	 */
	public function setTitle(string $title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getText(): string
	{
		return $this->text;
	}

	/**
	 * @param string $text
	 * @return Box
	 */
	public function setText(string $text): self
	{
		$this->text = $text;

		return $this;
	}

	/**
	 * @return null|string
	 */
	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * @return int
	 */
	public function getRank(): int
	{
		return $this->rank;
	}

	/**
	 * @param int $rank
	 * @return Box
	 */
	public function setRank(int $rank): self
	{
		$this->rank = $rank;

		if ($rank > 100) {
			$this->rank = 100;
		} elseif ($rank < 0) {
			$this->rank = 0;
		}

		return $this;
	}

	/**
	 * @return Step[]
	 */
	public function getSteps(): array
	{
		return $this->steps ?? [];
	}

	/**
	 * @param Step[] $steps
	 * @throws \InvalidArgumentException
	 */
	public function setSteps(array $steps): void
	{
		foreach ($steps as $step) {
			if (!($step instanceof Step)) {
				throw new \InvalidArgumentException('Given step is not valid. Did you set array of Step[]?');
			}
		}

		$this->steps = $steps;
	}

	/**
	 * @param Step $step
	 */
	public function addStep(Step $step): void
	{
		$this->steps[] = $step;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

}

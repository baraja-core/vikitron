<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Entity;


use InvalidArgumentException;
use Mathematicator\Engine\Step\Step;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;
use RuntimeException;

final class Box
{
	use SmartObject;

	public const TYPE_UNDEFINED = 'type_undefined';

	public const TYPE_INTERPRET = 'type_interpret';

	public const TYPE_TEXT = 'type_text';

	public const TYPE_LATEX = 'type_latex';

	public const TYPE_HTML = 'type_html';

	public const TYPE_KEYWORD = 'type_keyword';

	public const TYPE_IMAGE = 'type_image';

	public const TYPE_GRAPH = 'type_graph';

	public const TYPE_TABLE = 'type_table';

	/** @var string */
	private $type;

	/** @var string|null */
	private $icon;

	/** @var string|null */
	private $title;

	/** @var string|null */
	private $text;

	/** @var string|null */
	private $url;

	/** @var int */
	private $rank;

	/** @var Step[] */
	private $steps = [];

	/**
	 * Internal technical identifier.
	 *
	 * @var string|null
	 */
	private $tag;


	/**
	 * @param string $type
	 * @param string|null $title
	 * @param string|null $text
	 * @param string|null $url
	 * @param int $rank
	 */
	public function __construct(string $type = self::TYPE_UNDEFINED, ?string $title = null, ?string $text = null, ?string $url = null, int $rank = 32)
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
			? htmlspecialchars((string) $this->text, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8')
			: '';
	}


	/**
	 * @param mixed[] $table
	 * @return Box
	 * @throws JsonException
	 */
	public function setTable(array $table): self
	{
		$this->text = Json::encode($table);

		return $this;
	}


	/**
	 * @param string[]|int[] $table
	 * @return Box
	 */
	public function setKeyValue(array $table = []): self
	{
		if ($table !== []) {
			$items = [];

			foreach ($table as $key => $value) {
				$items[] = '<tr>'
					. '<th' . ($items === [] ? ' style="width:33%"' : '') . '>'
					. htmlspecialchars((string) $key, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8')
					. ':</th>'
					. '<td>' . htmlspecialchars((string) $value, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>'
					. '</tr>';
			}

			$this->text = '<table>' . implode('', $items) . '</table>';
		}

		return $this;
	}


	/**
	 * @return string
	 */
	public function getIcon(): string
	{
		if ($this->icon === null) {
			$icon = $this->type === self::TYPE_IMAGE ? 'fas fa-image' : 'fas fa-hashtag';
		} else {
			$icon = $this->icon;
		}

		return '<i class="' . $icon . '"></i>';
	}


	/**
	 * @param string $icon
	 * @return Box
	 */
	public function setIcon(string $icon): self
	{
		if (preg_match('/^(fas?)\s+(fa-[a-z0-9\-]+)$/', Strings::normalize($icon), $parser)) {
			$this->icon = $parser[1] . ' ' . $parser[2];
		} else {
			throw new RuntimeException('Icon "' . $icon . '" is not valid FontAwesome icon. Use format "fas fa-xxx".');
		}

		return $this;
	}


	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title ?? '';
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
		return $this->text ?? '';
	}


	/**
	 * @param mixed $text
	 * @return Box
	 */
	public function setText($text): self
	{
		$this->text = (string) $text;

		return $this;
	}


	/**
	 * @return string|null
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
	 * @return string|null
	 * @internal
	 */
	public function getTag(): ?string
	{
		return $this->tag;
	}


	/**
	 * @param string|null $tag
	 * @return Box
	 * @internal
	 */
	public function setTag(?string $tag): self
	{
		$this->tag = $tag;

		return $this;
	}


	/**
	 * @return Step[]
	 */
	public function getSteps(): array
	{
		return $this->steps;
	}


	/**
	 * First check all given items. When all items is type for Step, replace current step array.
	 *
	 * @param Step[]|mixed[] $steps
	 * @return Box
	 * @throws InvalidArgumentException
	 */
	public function setSteps(array $steps): self
	{
		$return = [];

		foreach ($steps as $step) {
			if (!$step instanceof Step) {
				throw new InvalidArgumentException('Step must be instance of "' . Step::class . '".');
			}
			$return[] = $step;
		}

		$this->steps = [];
		foreach ($return as $stepItem) {
			$this->addStep($stepItem);
		}

		return $this;
	}


	/**
	 * @param Step $step
	 * @return Box
	 */
	public function addStep(Step $step): self
	{
		$this->steps[] = $step;

		return $this;
	}


	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}
}

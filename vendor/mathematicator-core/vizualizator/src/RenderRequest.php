<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Nette\SmartObject;

final class RenderRequest
{
	use SmartObject;

	/** @var Renderer */
	private $renderer;

	/** @var int */
	private $width;

	/** @var int */
	private $height;

	/** @var string|null */
	private $title;

	/** @var mixed[] */
	private $border = [
		'size' => 0,
		'color' => 'black',
	];

	/** @var int[]|int[][]|int[][][]|null[] */
	private $lines = [];


	/**
	 * @param Renderer $renderer
	 * @param int $width
	 * @param int $height
	 */
	public function __construct(Renderer $renderer, int $width, int $height)
	{
		if ($width < 1) {
			$width = 1;
		}

		if ($height < 1) {
			$height = 1;
		}

		$this->renderer = $renderer;
		$this->width = $width;
		$this->height = $height;
	}


	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->render();
	}


	/**
	 * Smart shortcut.
	 *
	 * @param string $format
	 * @return string
	 */
	public function render(string $format = Renderer::FORMAT_PNG): string
	{
		return $this->renderer->render($this, $format);
	}


	/**
	 * @return string
	 */
	public function getSerialized(): string
	{
		return json_encode([
			'width' => $this->width,
			'height' => $this->height,
			'title' => $this->title,
			'lines' => $this->getLines(),
		]);
	}


	/**
	 * @return int
	 */
	public function getWidth(): int
	{
		return $this->width;
	}


	/**
	 * @return int
	 */
	public function getHeight(): int
	{
		return $this->height;
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
	 * @return RenderRequest
	 */
	public function setTitle(?string $title): self
	{
		$this->title = $title ?: null;

		return $this;
	}


	/**
	 * @return mixed[]
	 */
	public function getBorder(): array
	{
		return $this->border;
	}


	/**
	 * @param int|null $size
	 * @param string|null $color
	 * @return RenderRequest
	 */
	public function setBorder(?int $size = null, ?string $color = null): self
	{
		if ($size !== null && $size < 0) {
			$size = 0;
		}

		$this->border = [
			'size' => $size ?? 0,
			'color' => $color ?? 'black',
		];

		return $this;
	}


	/**
	 * @return int[][]|null[][]|int[][][]
	 */
	public function getLines(): array
	{
		$lines = $this->lines;

		if ($this->border['size'] > 0) {
			$color = $this->processColor($this->border['color']);
			$lines[] = [ // top
				'x' => 1,
				'y' => 1,
				'a' => $this->width - 1,
				'b' => 1,
				'color' => $color,
			];
			$lines[] = [ // left
				'x' => 1,
				'y' => 1,
				'a' => 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
			$lines[] = [ // right
				'x' => $this->width - 1,
				'y' => 1,
				'a' => $this->width - 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
			$lines[] = [ // bottom
				'x' => 1,
				'y' => $this->height - 1,
				'a' => $this->width - 1,
				'b' => $this->height - 1,
				'color' => $color,
			];
		}

		return $lines;
	}


	public function addLine(int $x, int $y, int $a, int $b, ?string $color = null): self
	{
		$this->lines[] = [
			'x' => $x,
			'y' => $y,
			'a' => $a,
			'b' => $b,
			'color' => $this->processColor($color),
		];

		return $this;
	}


	/**
	 * @param string|null $color
	 * @return int[]|null
	 */
	private function processColor(?string $color): ?array
	{
		if ($color === null) {
			return null;
		}

		static $names = [
			'black' => [0, 0, 0],
			'white' => [255, 255, 255],
			'red' => [255, 0, 0],
			'green' => [0, 255, 0],
			'blue' => [0, 0, 255],
		];

		if (isset($names[$color = trim($color)])) { // 1. Named color
			return $names[$color];
		}

		if (($color[0] ?? '') === '#' && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color, $hex)) { // HTML hex
			if (($match = strtolower($hex[1])) && isset($match[3]) === false) {
				$r = $match[0] . $match[0];
				$g = $match[1] . $match[1];
				$b = $match[2] . $match[2];
			} else {
				$r = $match[0] . $match[1];
				$g = $match[2] . $match[3];
				$b = $match[4] . $match[5];
			}

			return [hexdec($r), hexdec($g), hexdec($b)];
		}

		return null;
	}
}

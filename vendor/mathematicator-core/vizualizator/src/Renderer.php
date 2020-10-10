<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


use Mathematicator\Vizualizator\Compiler\JpgCompiler;
use Mathematicator\Vizualizator\Compiler\PngCompiler;
use Mathematicator\Vizualizator\Compiler\SvgICompiler;

final class Renderer
{
	public const FORMAT_SVG = 'svg';

	public const FORMAT_PNG = 'png';

	public const FORMAT_JPG = 'jpg';


	/**
	 * @param int $width
	 * @param int $height
	 * @return RenderRequest
	 */
	public function createRequest(int $width = 500, int $height = 500): RenderRequest
	{
		return new RenderRequest($this, $width, $height);
	}


	/**
	 * @param RenderRequest $request
	 * @param string $format
	 * @return string
	 */
	public function render(RenderRequest $request, string $format = self::FORMAT_PNG): string
	{
		switch ($format) {
			case self::FORMAT_PNG:
				$compiler = new PngCompiler();
				$contentType = 'image/png';
				break;
			case self::FORMAT_JPG:
				$compiler = new JpgCompiler();
				$contentType = 'image/jpeg';
				break;
			case self::FORMAT_SVG:
			default:
				$compiler = new SvgICompiler();
				$contentType = null;
		}

		if (($content = $compiler->compile($request)) !== '') {
			if (($title = $request->getTitle()) !== null) {
				$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
			}

			if ($contentType !== null) {
				$return = '<img src="data:' . $contentType . ';base64,' . base64_encode($content) . '"'
					. ($title !== null ? ' alt="' . $title . '" title="' . $title . '"' : '')
					. '>';
			} else {
				$return = $content;
			}
		} else {
			$return = '';
		}

		$styles = [
			'width:' . $request->getWidth() . 'px',
			'height:' . $request->getHeight() . 'px',
		];

		return '<div class="vizualizator" style="' . implode(';', $styles) . '">' . $return . '</div>';
	}


	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function renderSvg(RenderRequest $request): string
	{
		return $this->render($request, self::FORMAT_SVG);
	}


	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function renderJpg(RenderRequest $request): string
	{
		return $this->render($request, self::FORMAT_JPG);
	}


	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function renderPng(RenderRequest $request): string
	{
		return $this->render($request, self::FORMAT_PNG);
	}
}

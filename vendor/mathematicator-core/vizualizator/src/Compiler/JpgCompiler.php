<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator\Compiler;


use Mathematicator\Vizualizator\RenderRequest;

final class JpgCompiler extends PhpGDICompiler implements ICompiler
{

	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string
	{
		$image = imagecreatetruecolor($request->getWidth(), $request->getHeight());

		if ($image === false) {
			throw new \Exception('Image cannot be created!');
		}

		$this->image = $image;

		imagefill($this->image, 0, 0, $this->getColor(255, 255, 255));

		$this->process($request);

		imagesavealpha($this->image, true);

		ob_start();
		imagejpeg($this->image);
		$bin = ob_get_clean();

		imagedestroy($this->image);

		return (string) $bin;
	}
}

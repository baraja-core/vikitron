<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


class JpgCompiler extends PhpGDCompiler
{

	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string
	{
		$this->image = imagecreatetruecolor($request->getWidth(), $request->getHeight());
		imagefill($this->image, 0, 0, $this->getColor(255, 255, 255));

		$this->process($request);

		imagesavealpha($this->image, true);

		ob_start();
		imagejpeg($this->image);
		$bin = ob_get_clean();

		imagedestroy($this->image);

		return $bin;
	}

}
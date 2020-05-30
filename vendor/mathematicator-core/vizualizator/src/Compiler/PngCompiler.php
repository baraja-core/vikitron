<?php

declare(strict_types=1);

namespace Mathematicator\Vizualizator;


final class PngCompiler extends PhpGDCompiler
{

	/**
	 * @param RenderRequest $request
	 * @return string
	 */
	public function compile(RenderRequest $request): string
	{
		$this->image = imagecreatetruecolor($request->getWidth(), $request->getHeight());
		imagealphablending($this->image, false);
		imagesavealpha($this->image, true);
		$alphaColor = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
		imagefill($this->image, 0, 0, $alphaColor);

		$this->process($request);

		imagesavealpha($this->image, true);

		ob_start();
		imagepng($this->image);
		$bin = ob_get_clean();

		imagedestroy($this->image);

		return $bin;
	}
}
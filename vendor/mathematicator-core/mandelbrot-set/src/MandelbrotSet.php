<?php

declare(strict_types=1);

namespace Mathematicator\MandelbrotSet;


use Exception;
use Nette\Utils\FileSystem;

/**
 * Calculate and render Mandelbrot set as image to file.
 * Implementation was inspired from Pavol Hejny, https://www.pavolhejny.com/.
 */
final class MandelbrotSet
{

	/** @var string */
	private $tempDir;


	public function __construct(string $tempDir)
	{
		ini_set('max_execution_time', '100000');
		FileSystem::createDir($tempDir);
		$this->tempDir = $tempDir;
	}


	/**
	 * Load image from temp by Request and return as base64 image.
	 */
	public function loadImage(MandelbrotSetRequest $request): string
	{
		if (is_file($path = $this->tempDir . '/' . $request->getFileName()) === false) {
			$this->generate($request);
		}

		return 'data:' . mime_content_type($path) . ';base64,' . base64_encode((string) file_get_contents($path));
	}


	/**
	 * Process image by request and save to temp file.
	 */
	public function generate(MandelbrotSetRequest $request): void
	{
		[$w, $h, $itt, $min_x, $max_x, $min_y, $max_y, $d1, $d2] = $request->getParams();

		$dim_x = $w;
		$dim_y = $h;
		$im = imagecreatetruecolor((int) $dim_x, (int) $dim_y);

		if ($im === false) {
			throw new Exception('Image cannot be created.');
		}

		imagealphablending($im, false);
		imagesavealpha($im, true);

		$blackColor = imagecolorallocate($im, 0, 0, 0);
		$alpha_color = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $alpha_color);

		for ($y = 0; $y <= $dim_y; $y++) { // browsing and evaluating each point
			for ($x = 0; $x <= $dim_x; $x++) { // find the coordinates of the point that is added in each iteration
				$c1 = $min_x + ($max_x - $min_x) / $dim_x * $x;
				$c2 = $min_y + ($max_y - $min_y) / $dim_y * $y;
				$z1 = 0; // current number
				$z2 = 0;

				for ($i = 0; $i < $itt; $i++) { // main iterator
					// finding the distance from 0 + 0i
					$distance = sqrt($z1 * $z1 + $z2 * $z2);

					if ((int) $distance !== 0) {
						$angle = acos($z1 / $distance);
					} else {
						$angle = 0;
					}

					if ($z2 < 0) { // angle
						$angle = (2 * M_PI) - $angle;
					}

					$angle *= $d1; // multiply the angle
					$distance = $distance ** $d2; // power of distance
					// calculation of the new x, y
					$z1 = cos($angle) * $distance;
					$z2 = sin($angle) * $distance;
					// adding point coordinates
					$z1 += $c1;
					$z2 += $c2;

					// if the point is at a distance of 2 or greater, the point will not be in the set and the iteration can be terminated
					if ($z1 * $z1 + $z2 * $z2 >= 4) {
						break;
					}
				}

				// if in each iteration he held a new point at a distance of 2 or less, the original point is filled.
				if ($i >= $itt) {
					imagesetpixel($im, (int) round($x), (int) round($y), $blackColor);
				}
			}
		}

		// Save to file
		imagesavealpha($im, true);
		imagepng($im, $path = $this->tempDir . '/' . $request->getFileName());
		imagedestroy($im);
	}
}

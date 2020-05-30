<?php

declare(strict_types=1);

namespace Mathematicator\MandelbrotSet;

/**
 * Calculate and render Mandelbrot set as image to file.
 * Implementation is inspired by Pavol Hejny, https://www.pavolhejny.com/.
 */
final class MandelbrotSet
{

	/** @var string */
	private $tempDir;


	/**
	 * @param string $tempDir
	 * @throws \Exception
	 */
	public function __construct(string $tempDir)
	{
		ini_set('max_execution_time', '100000');

		if (!is_dir($tempDir) && !@mkdir($tempDir, 0777, true) && !is_dir($tempDir)) { // @ - dir may already exist
			throw new \Exception(
				'Unable to create directory "' . $tempDir . '". '
				. preg_replace('#^\w+\(.*?\): #', '', error_get_last()['message'])
			);
		}

		$this->tempDir = $tempDir;
	}


	/**
	 * Load image from temp by Request and return as base64 image.
	 *
	 * @param MandelbrotSetRequest $request
	 * @return string
	 */
	public function loadImage(MandelbrotSetRequest $request): string
	{
		if (is_file($path = $this->tempDir . '/' . $request->getFileName()) === false) {
			$this->generate($request);
		}

		return 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
	}


	/**
	 * Process image by request and save to temp file.
	 *
	 * @param MandelbrotSetRequest $request
	 */
	public function generate(MandelbrotSetRequest $request): void
	{
		[$w, $h, $itt, $min_x, $max_x, $min_y, $max_y, $d1, $d2] = $request->getParams();

		$dim_x = $w;
		$dim_y = $h;
		$im = imagecreatetruecolor((int) $dim_x, (int) $dim_y);
		imagealphablending($im, false);
		imagesavealpha($im, true);

		$blackColor = imagecolorallocate($im, 0, 0, 0);
		$alpha_color = imagecolorallocatealpha($im, 0, 0, 0, 127);
		imagefill($im, 0, 0, $alpha_color);

		for ($y = 0; $y <= $dim_y; $y++) { // Procházení a vyhodnocení každého bodu
			for ($x = 0; $x <= $dim_x; $x++) { // Zjištění souřadnic bodu, který se přičte v každé iteraci
				$c1 = $min_x + ($max_x - $min_x) / $dim_x * $x;
				$c2 = $min_y + ($max_y - $min_y) / $dim_y * $y;
				$z1 = 0; // aktuální číslo
				$z2 = 0;

				for ($i = 0; $i < $itt; $i++) { // Main iterator
					// Zjištění vzdálenosti od 0+0i
					$distance = sqrt($z1 * $z1 + $z2 * $z2);

					if ((int) $distance !== 0) {
						$angle = acos($z1 / $distance);
					} else {
						$angle = 0;
					}

					if ($z2 < 0) { // Úhel
						$angle = (2 * M_PI) - $angle;
					}

					$angle *= $d1; // Vynásobení úhlu
					$distance = $distance ** $d2; // Mocnění vzdálenosti
					// Výpočet nového x,y
					$z1 = cos($angle) * $distance;
					$z2 = sin($angle) * $distance;
					// Přičtení souřadnic bodu
					$z1 += $c1;
					$z2 += $c2;

					// Pokud je bod ve vzdálenosti 2 nebo větší, bod v množině nebude a iterování lze ukončit
					if ($z1 * $z1 + $z2 * $z2 >= 4) {
						break;
					}
				}

				// Pokud v každé iteraci držel nový bod ve vzdálenosti 2 nebo méně, je původní bod vyplněn.
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


	/**
	 * @deprecated
	 * @param int $width
	 * @param int $height
	 */
	public function renderAll(int $width = 300, int $height = 300): void
	{
		$minX = -2; // Levý okraj
		$maxX = 1; // Pravý okraj
		$minY = ($height * ($maxX - $minX) / $width) / -2;// Horní okraj
		$maxY = ($height * ($maxX - $minX) / $width) / 2;// Dolní okraj

		// čísla, kterými se bude násobit uhel
		$ax = [];
		for ($x = -7; $x <= 3; $x++) {
			$ax[] = $x;
		}
		sort($ax);

		$ay = []; // čísla, kterými se bude mocnit vzdálenost
		for ($y = 0; $y <= 4; $y++) {
			$ay[] = $y;
		}
		sort($ay);

		array_unshift($ax, false);
		array_unshift($ay, false);

		echo '<table>';
		foreach ($ay as $d2) {
			echo '<tr>';
			foreach ($ax as $d1) {
				if ($d1 !== false && $d2 !== false) {
					$request = new MandelbrotSetRequest($d1, $d2, $width, $height, 18, $minX, $maxX, $minY, $maxY);

					if ($d1 * $d2 % 2) {
						$bgColor = 'eeeeee';
					} elseif ($d1 % 2) {
						$bgColor = 'f5f5f5';
					} elseif ($d2 % 2) {
						$bgColor = 'f5f5f5';
					} else {
						$bgColor = 'fbfbfb';
					}

					echo '<td bgcolor="' . $bgColor . '"><img src="' . $this->loadImage($request) . '" border="0"></td>';
				} elseif ($d1 === false && $d2 !== false) { // Zobrazení popisku osy
					echo '<td valign="middle">';

					if ((int) round($d2) === (int) $d2) {
						echo '<h2>^' . $d2 . '&nbsp;</h2>';
					} else {
						echo '^' . $d2 . '&nbsp;';
					}

					echo '</td>';
				} elseif ($d2 === false && $d1 !== false) { // Zobrazení popisku osy
					echo '<td align="center">';

					if ((int) round($d1) === (int) $d1) {
						echo '<h2>*' . $d1 . '</h2>';
					} else {
						echo '*' . $d1 . '';
					}

					echo '</td>';
				} else {
					echo '<td>&nbsp;</td>';
				}
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Helper;


use Mathematicator\Engine\MathematicatorException;
use Nette\StaticClass;

class DateTime
{

	use StaticClass;

	/**
	 * Format datetime to "Y-m-d H:i:s", if null return current datetime.
	 *
	 * @param int|null $timestamp
	 * @return string (Y-m-d H:i:s)
	 */
	public static function getDateTimeIso(int $timestamp = null): string
	{
		return date('Y-m-d H:i:s', $timestamp ?? \time());
	}


	/**
	 * @param int $time
	 * @param bool $moreAccurate
	 * @param string $lang [cz, sk, en]
	 * @return string
	 * @throws MathematicatorException
	 */
	public static function formatTimeAgo(int $time, bool $moreAccurate = true, string $lang = 'cz'): string
	{
		if ($lang === 'cz') {
			$labels = [
				['sekunda', 'sekundy', 'sekund'],
				['minuta', 'minuty', 'minut'],
				['hodina', 'hodiny', 'hodin'],
				['den', 'dny', 'dní'],
				['měsíc', 'měsíce', 'měsíců'],
				['rok', 'roky', 'let'],
			];
		} elseif ($lang === 'sk') {
			$labels = [
				['sekunda', 'sekundy', 'sekúnd'],
				['minúta', 'minúty', 'minút'],
				['hodina', 'hodiny', 'hodín'],
				['deň', 'dni', 'dní'],
				['mesiac', 'mesiace', 'mesiacov'],
				['rok', 'roky', 'rokov'],
			];
		} elseif ($lang === 'en') {
			$labels = ['second', 'minute', 'hour', 'day', 'month', 'year'];
		} else {
			throw new MathematicatorException('Unsupported lang "' . $lang . '" (supported languages: cz/sk/en)');
		}

		$currentTime = time();
		$diff = $currentTime - $time;
		$no = 0;
		$lengths = [1, 60, 3600, 86400, 2630880, 31570560];
		$v = \count($lengths) - 1;

		for (true; ($v >= 0) && (($no = $diff / $lengths[$v]) <= 1); true) {
			$v--;
		}

		if ($v < 0) {
			$v = 0;
		}

		$x = $currentTime - ($diff % $lengths[$v]);
		$no = (int) floor($no);
		$label = null;

		if (isset($labels[$v]) && \is_string($labels[$v])) {
			$label = $labels[$v];
			if ($lang === 'en' && $no !== 1) {
				$label .= 's';
			}
		} elseif (\is_array($labels[$v])) {
			if ($no === 1) {
				$label = $labels[$v][0];
			} elseif ($no >= 2 && $no <= 4) {
				$label = $labels[$v][1];
			} else {
				$label = $labels[$v][2];
			}
		}

		$result = $no . ' ' . $label . ' ';
		if ($moreAccurate && ($v >= 1) && (($currentTime - $x) > 0)) {
			$result .= self::formatTimeAgo($x, false, $lang);
		}

		return trim($result);
	}

}
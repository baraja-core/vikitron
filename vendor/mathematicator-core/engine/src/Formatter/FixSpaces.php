<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Formatter;


final class FixSpaces
{

	/** @var string[] */
	private static $rules = [
		'(\s|;|^)(\w)\s' => '$1$2&nbsp;',
		'(\d)\s(let|rok.*?|g|kg|m|mm|h|hod|hodi.+?|m|min|minu.+?|s|sekun.+?|sec|second|milio.+?|miliar.+?|kč|Kč|°)([^\w])' => '$1&nbsp;$2$3',
		'(\d)\s*(\%)' => '$1&nbsp;$2',
		'(\d)\s+(\d{3})([^\d]|$)' => '$1&nbsp;$2$3',
		'(\d)\.\s+(\d)' => '$1.&nbsp;$2',
		'([A-Z\d]{2,})\s(\w)' => '$1&nbsp;$2',
		'\s([-–])' => '&nbsp;$1',
		'([§\*†©])\s' => '$1&nbsp;',
	];


	public static function fix(string $haystack): string
	{
		$haystack = (string) preg_replace('/(\&nbsp\;|\s)+/', ' ', $haystack);
		$iterator = 0;

		while (true) {
			$original = $haystack;
			foreach (self::$rules as $pattern => $replacement) {
				$haystack = (string) preg_replace('/' . $pattern . '/', $replacement, $haystack);
			}

			$iterator++;
			if ($haystack === $original || $iterator > 10) {
				break;
			}
		}

		return $haystack;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator;


class FixSpaces
{

	/**
	 * @var string[]
	 */
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

	/**
	 * @param string $content
	 * @return mixed|string
	 */
	public function fix(string $content)
	{
		$content = (string) preg_replace('/(\&nbsp\;|\s)+/', ' ', $content);
		$iterator = 0;

		while (true) {
			$origin = $content;
			foreach (self::$rules as $pattern => $replacement) {
				$content = (string) preg_replace('/' . $pattern . '/', $replacement, $content);
			}

			$iterator++;
			if ($content === $origin || $iterator > 10) {
				break;
			}
		}

		return $content;
	}

}

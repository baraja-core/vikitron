<?php

namespace Model\Math;

class FixSpaces
{

	/**
	 * @var string[]
	 */
	private $rules = [
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
	 * @param array $rules
	 */
	public function __construct(array $rules = [])
	{
		$this->rules = $rules === [] ? $this->rules : array_merge($this->rules, $rules);
	}

	/**
	 * @param string $content
	 * @return mixed|string
	 */
	public function fix(string $content)
	{
		$content = preg_replace('/(\&nbsp\;|\s)+/', ' ', $content);
		$iterator = 0;

		while (true) {
			$origin = $content;
			foreach ($this->rules as $pattern => $replacement) {
				$content = preg_replace('/' . $pattern . '/', $replacement, $content);
			}

			$iterator++;
			if ($content === $origin || $iterator > 10) {
				break;
			}
		}

		return $content;
	}

}

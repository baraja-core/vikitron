<?php

declare(strict_types=1);

namespace Mathematicator\Search;


use Nette\StaticClass;

class TextRenderer
{

	use StaticClass;

	/**
	 * Reserved for future use.
	 *
	 * @param string $haystack
	 * @return string
	 */
	public static function process(string $haystack): string
	{
		return htmlspecialchars($haystack);
	}

}
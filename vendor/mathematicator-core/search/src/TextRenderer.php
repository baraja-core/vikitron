<?php

declare(strict_types=1);

namespace Mathematicator\Search;


final class TextRenderer
{

	/** @throws \Error */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * Reserved for future use.
	 */
	public static function process(string $haystack): string
	{
		return htmlspecialchars($haystack);
	}
}

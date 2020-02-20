<?php

/**
 * This file is part of the Texy! (https://texy.info)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Texy;


/**
 * Link.
 */
final class Link
{
	use Strict;

	/** @see $type */
	public const
		COMMON = 1,
		BRACKET = 2,
		IMAGE = 3;

	/** @var string|null  URL in resolved form */
	public $URL;

	/** @var string  URL as written in text */
	public $raw;

	/** @var Modifier|null */
	public $modifier;

	/** @var int  how was link created? */
	public $type = self::COMMON;

	/** @var string|null  optional label, used by references */
	public $label;

	/** @var string|null  reference name (if is stored as reference) */
	public $name;


	public function __construct(string $URL)
	{
		$this->URL = $URL;
		$this->raw = $URL;
		$this->modifier = new Modifier;
	}


	public function __clone()
	{
		if ($this->modifier) {
			$this->modifier = clone $this->modifier;
		}
	}
}

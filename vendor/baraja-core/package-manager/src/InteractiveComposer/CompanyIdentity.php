<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


/**
 * Implementing class must be type of CompanyIdentity
 * and name of class must end with "Identity" suffix.
 */
interface CompanyIdentity
{

	/**
	 * Return unique company logo with some information.
	 *
	 * @return string
	 */
	public function getLogo(): string;
}

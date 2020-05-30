<?php

declare(strict_types=1);

namespace Baraja\Localization;


/**
 * Static helper defined by DIC.
 * This feature is reserved for use in Doctrine entities.
 *
 * @internal
 */
final class LocalizationHelper
{

	/** @var Localization|null */
	private static $localization;


	/**
	 * Get current locale if localization matched.
	 *
	 * @internal
	 * @param bool $fallbackToContextLocale
	 * @return string
	 */
	public static function getLocale(bool $fallbackToContextLocale = false): string
	{
		return self::getLocalization()->getLocale($fallbackToContextLocale);
	}


	/**
	 * @internal
	 * @return string[][]
	 */
	public static function getFallbackLocales(): array
	{
		return self::getLocalization()->getFallbackLocales();
	}


	/**
	 * @internal
	 * @return Localization
	 */
	public static function getLocalization(): Localization
	{
		if (self::$localization === null) {
			throw new LocalizationException('Localization have been not defined.');
		}

		return self::$localization;
	}


	/**
	 * @internal for DIC
	 * @param Localization $localization
	 */
	public static function setLocalization(Localization $localization): void
	{
		self::$localization = $localization;
	}
}
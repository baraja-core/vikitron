<?php

declare(strict_types=1);

namespace Baraja\Localization;


final class LocalizationException extends \RuntimeException
{
	public static function canNotResolveLocale(?string $defined, ?string $parameter, ?string $domain): void
	{
		throw new self(
			'Can not resolve current locale. Explored inputs:' . "\n"
			. 'Defined: "' . ($defined ?? 'null') . '", URL parameter: "' . ($parameter ?? 'null') . '", domain: "' . ($domain ?? 'null') . '".' . "\n"
			. 'Did you defined default locale for all domains or use router rewriting?'
		);
	}


	public static function contextLocaleIsEmpty(?string $currentLocale): void
	{
		throw new self(
			'Context locale is empty.'
			. ($currentLocale !== null ? ' Did you mean current locale "' . $currentLocale . '"?' : '')
		);
	}


	public static function tableDoesNotExist(): void
	{
		throw new self(
			'Localization database tables does not exist. Please create tables and insert default configuration first.' . "\n"
			. 'To solve this issue: Please create tables ("core__localization_domain" and "core__localization_locale") with default data.'
		);
	}


	public static function domainListIsEmpty(): void
	{
		throw new self(
			'Domain list is empty. Please define project domains to table "core__localization_domain".'
		);
	}
}

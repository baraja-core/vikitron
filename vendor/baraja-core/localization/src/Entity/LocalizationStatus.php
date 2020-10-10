<?php

declare(strict_types=1);

namespace Baraja\Localization;


use Nette\SmartObject;

final class LocalizationStatus
{
	use SmartObject;

	/** @var string[] */
	private $availableLocales;

	/** @var string */
	private $defaultLocale;

	/** @var string[][] */
	private $fallbackLocales;

	/** @var string[]|null[] */
	private $localeToTitleSuffix;

	/** @var string[]|null[] */
	private $localeToTitleSeparator;

	/** @var string[]|null[] */
	private $localeToTitleFormat;

	/** @var string[]|null[] */
	private $localeToSiteName;

	/** @var string[] */
	private $domainToLocale;

	/** @var string[] */
	private $domainToEnvironment;

	/** @var bool[] */
	private $domainToProtected;

	/** @var string[] */
	private $domainToScheme;

	/** @var bool[] */
	private $domainToUseWww;

	/** @var string[][] */
	private $domainByEnvironment;

	/** @var mixed[][]|mixed[][][] */
	private $domains;


	/**
	 * @param string[] $availableLocales
	 * @param string $defaultLocale
	 * @param string[][] $fallbackLocales
	 * @param string[]|null[] $localeToTitleSuffix
	 * @param string[]|null[] $localeToTitleSeparator
	 * @param string[]|null[] $localeToTitleFormat
	 * @param string[]|null[] $localeToSiteName
	 * @param string[] $domainToLocale
	 * @param string[] $domainToEnvironment
	 * @param bool[] $domainToProtected
	 * @param string[] $domainToScheme
	 * @param bool[] $domainToUseWww
	 * @param string[][] $domainByEnvironment
	 * @param mixed[][]|mixed[][][] $domains
	 */
	public function __construct(array $availableLocales, string $defaultLocale, array $fallbackLocales, array $localeToTitleSuffix, array $localeToTitleSeparator, array $localeToTitleFormat, array $localeToSiteName, array $domainToLocale, array $domainToEnvironment, array $domainToProtected, array $domainToScheme, array $domainToUseWww, array $domainByEnvironment, array $domains)
	{
		$this->availableLocales = $availableLocales;
		$this->defaultLocale = $defaultLocale;
		$this->fallbackLocales = $fallbackLocales;
		$this->localeToTitleSuffix = $localeToTitleSuffix;
		$this->localeToTitleSeparator = $localeToTitleSeparator;
		$this->localeToTitleFormat = $localeToTitleFormat;
		$this->localeToSiteName = $localeToSiteName;
		$this->domainToLocale = $domainToLocale;
		$this->domainToEnvironment = $domainToEnvironment;
		$this->domainToProtected = $domainToProtected;
		$this->domainToScheme = $domainToScheme;
		$this->domainToUseWww = $domainToUseWww;
		$this->domainByEnvironment = $domainByEnvironment;
		$this->domains = $domains;
	}


	/**
	 * @return string[]
	 */
	public function getAvailableLocales(): array
	{
		return $this->availableLocales;
	}


	/**
	 * @return string
	 */
	public function getDefaultLocale(): string
	{
		return $this->defaultLocale;
	}


	/**
	 * @return string[][]
	 */
	public function getFallbackLocales(): array
	{
		return $this->fallbackLocales;
	}


	/**
	 * @return string[]|null[]
	 */
	public function getLocaleToTitleSuffix(): array
	{
		return $this->localeToTitleSuffix;
	}


	/**
	 * @return string[]|null[]
	 */
	public function getLocaleToTitleSeparator(): array
	{
		return $this->localeToTitleSeparator;
	}


	/**
	 * @return string[]|null[]
	 */
	public function getLocaleToTitleFormat(): array
	{
		return $this->localeToTitleFormat;
	}


	/**
	 * @return string[]|null[]
	 */
	public function getLocaleToSiteName(): ?array
	{
		return $this->localeToSiteName;
	}


	/**
	 * @return string[]
	 */
	public function getDomainToLocale(): array
	{
		return $this->domainToLocale;
	}


	/**
	 * @return string[]
	 */
	public function getDomainToEnvironment(): array
	{
		return $this->domainToEnvironment;
	}


	/**
	 * @return bool[]
	 */
	public function getDomainToProtected(): array
	{
		return $this->domainToProtected;
	}


	/**
	 * @return string[]
	 */
	public function getDomainToScheme(): array
	{
		return $this->domainToScheme;
	}


	/**
	 * @return bool[]
	 */
	public function getDomainToUseWww(): array
	{
		return $this->domainToUseWww;
	}


	/**
	 * @return string[][]
	 */
	public function getDomainByEnvironment(): array
	{
		return $this->domainByEnvironment;
	}


	/**
	 * @return mixed[][]|mixed[][][]
	 */
	public function getDomains(): array
	{
		return $this->domains;
	}
}

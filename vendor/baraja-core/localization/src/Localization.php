<?php

declare(strict_types=1);

namespace Baraja\Localization;


use Baraja\Doctrine\EntityManager;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Http\Request;

final class Localization
{

	/** @var EntityManager */
	private $entityManager;

	/** @var Cache */
	private $cache;

	/** @var string|null */
	private $localeDomain;

	/** @var string|null */
	private $localeParameter;

	/** @var string|null */
	private $localeDefined;

	/** @var string|null */
	private $localeContext;

	/** @var string|null */
	private $currentDomain;

	/** @var LocalizationStatus|null */
	private $status;


	/**
	 * @param EntityManager $entityManager
	 * @param IStorage $storage
	 */
	public function __construct(EntityManager $entityManager, IStorage $storage)
	{
		$this->entityManager = $entityManager;
		$this->cache = new Cache($storage, 'baraja-localization');
	}


	/**
	 * Method return best locale for current request.
	 * Matching process use this strategy:
	 *
	 * 1. Defined locale by setLocale(), for example by router
	 * 2. Analyze of ?locale parameter in URL
	 * 3. Connected default locale to current domain
	 *
	 * If locale does not match, this logic throws exception.
	 *
	 * @param bool $fallbackToContextLocale
	 * @return string
	 */
	public function getLocale(bool $fallbackToContextLocale = false): string
	{
		if (PHP_SAPI === 'cli') {
			throw new \RuntimeException('Localization: Current locale is not available in CLI.');
		}

		if ($this->localeDomain === null) {
			$this->localeDomain = $this->getStatus()->getDomainToLocale()[$this->currentDomain] ?? null;
		}

		$locale = $this->localeDefined ?? $this->localeParameter ?? $this->localeDomain;

		if ($fallbackToContextLocale === true && $locale === null) { // Fallback only in case of unmatched locale
			if ($this->localeContext === null) {
				LocalizationException::contextLocaleIsEmpty($locale);
			}
			$locale = $this->localeContext;
		}

		if ($locale === null) {
			LocalizationException::canNotResolveLocale($this->localeDefined, $this->localeParameter, $this->localeDomain);
		}

		return $locale;
	}


	/**
	 * @internal use for routing or other locale logic.
	 * @param string $locale
	 * @return Localization
	 */
	public function setLocale(string $locale): self
	{
		$this->localeDefined = strtolower($locale);

		return $this;
	}


	/**
	 * @internal use for specific context cases, for example CMS manager.
	 * @param string $contextLocale
	 * @return Localization
	 */
	public function setContextLocale(string $contextLocale): self
	{
		$this->localeContext = strtolower($contextLocale);

		return $this;
	}


	/**
	 * @return string[]
	 */
	public function getAvailableLocales(): array
	{
		return $this->getStatus()->getAvailableLocales();
	}


	/**
	 * @return string
	 */
	public function getDefaultLocale(): string
	{
		return $this->getStatus()->getDefaultLocale();
	}


	/**
	 * Rewriting table for the most used languages sorted according to national customs.
	 * For example, if there is no Slovak, it is better to rewrite the language first to Czech and then to English.
	 *
	 * In format: [
	 *    'locale' => ['fallback', ...]
	 * ]
	 *
	 * For example: [
	 *    'cs' => ['sk', 'en']
	 * ]
	 *
	 * @return string[][]
	 */
	public function getFallbackLocales(): array
	{
		return $this->getStatus()->getFallbackLocales();
	}


	/**
	 * Define basic localization configuration by current HTTP request.
	 *
	 * Main localization match is defined by current domain (locale by domain detection).
	 * Secondary detection (for multiple locales within a single domain) is GET ?locale parameter.
	 *
	 * @internal for DIC.
	 * @param Request $request
	 */
	public function processHttpRequest(Request $request): void
	{
		if (PHP_SAPI === 'cli') {
			throw new \RuntimeException('Localization: Processing HTTP request is not available in CLI.');
		}

		$url = $request->getUrl();
		if (\is_string($localeParameter = $url->getQueryParameter('locale')) === true) {
			$this->localeParameter = $localeParameter;
		}
		$this->currentDomain = str_replace('www.', '', $url->getDomain(4));
	}


	/**
	 * Clear whole internal domain cache and return current relevant localize settings.
	 *
	 * @internal
	 */
	public function clearCache(): void
	{
		$this->cache->remove('configuration');
	}


	/**
	 * Create internal LocalizationStatus entity from cache.
	 *
	 * @return LocalizationStatus
	 */
	public function getStatus(): LocalizationStatus
	{
		if ($this->status !== null) {
			return $this->status;
		}

		if (($config = $this->cache->load('configuration')) === null) {
			$this->cache->save('configuration', $config = $this->createCache(), [
				Cache::EXPIRE => '30 minutes',
			]);
		}

		return $this->status = new LocalizationStatus(
			$config['availableLocales'],
			$config['defaultLocale'],
			$config['fallbackLocales'],
			$config['localeToTitleSuffix'],
			$config['localeToTitleSeparator'],
			$config['localeToTitleFormat'],
			$config['domainToLocale'],
			$config['domainToEnvironment'],
			$config['domainToProtected'],
			$config['domainToScheme'],
			$config['domainToUseWww'],
			$config['domainByEnvironment'],
			$config['domains']
		);
	}


	/**
	 * @param string $locale
	 * @return Locale
	 * @throws NoResultException|NonUniqueResultException
	 */
	public function getLocaleEntity(string $locale): Locale
	{
		return $this->entityManager->getRepository(Locale::class)
			->createQueryBuilder('locale')
			->where('locale.locale = :locale')
			->setParameter('locale', $locale)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult();
	}


	/**
	 * @return mixed[]
	 */
	private function createCache(): array
	{
		$defaultLocale = null;
		$availableLocales = [];
		$localeToTitleSuffix = [];
		$localeToTitleSeparator = [];
		$localeToTitleFormat = [];
		$domainToLocale = [];
		$domainToEnvironment = [];
		$domainIsProtected = [];
		$domainToScheme = [];
		$domainToUseWww = [];
		$domainByEnvironment = [];

		try {
			/** @var mixed[][]|mixed[][][] $domains */
			$domains = $this->entityManager->getRepository(Domain::class)
				->createQueryBuilder('domain')
				->select('domain, locale')
				->leftJoin('domain.locale', 'locale')
				->getQuery()
				->getArrayResult();
		} catch (TableNotFoundException $e) {
			LocalizationException::tableDoesNotExist();
			$domains = []; // For PhpStan
		}

		if ($domains === []) {
			LocalizationException::domainListIsEmpty();
		}

		foreach ($domains as $domain) {
			$domainToLocale[$domain['domain']] = $locale = (string) ($domain['locale']['locale'] ?? 'en');
			$domainToEnvironment[$domain['domain']] = (string) $domain['environment'];
			$domainIsProtected[$domain['domain']] = (bool) $domain['protected'];
			$domainToScheme[$domain['domain']] = ((bool) $domain['https']) === true ? 'https' : 'http';
			$domainToUseWww[$domain['domain']] = (bool) $domain['www'];
			if (isset($domainByEnvironment[$domain['environment']][$locale]) === false || $domain['default'] === true) {
				if (isset($domainByEnvironment[$domain['environment']]) === false) {
					$domainByEnvironment[$domain['environment']] = [];
				}
				$domainByEnvironment[$domain['environment']][$locale] = (string) $domain['domain'];
			}
		}

		$locales = $this->entityManager->getRepository(Locale::class)
			->createQueryBuilder('locale')
			->select('PARTIAL locale.{id, locale, default, titleSuffix, titleSeparator, titleFormat}')
			->where('locale.active = TRUE')
			->orderBy('locale.position', 'ASC')
			->getQuery()
			->getArrayResult();

		foreach ($locales as $locale) {
			$availableLocales[] = $locale['locale'];
			if ($locale['default'] === true) {
				if ($defaultLocale !== null) {
					trigger_error('Multiple default locales: Locale "' . $defaultLocale . '" and "' . $locale['locale'] . '" is marked as default.');
				} else {
					$defaultLocale = $locale['locale'];
				}
			}
			$localeToTitleSuffix[$locale['locale']] = $locale['titleSuffix'];
			$localeToTitleSeparator[$locale['locale']] = $locale['titleSeparator'];
			$localeToTitleFormat[$locale['locale']] = $locale['titleFormat'];
		}

		return [
			'availableLocales' => $availableLocales,
			'defaultLocale' => $defaultLocale,
			'fallbackLocales' => [], // TODO: Implement smart logic for get fallback languages by common convention.
			'localeToTitleSuffix' => $localeToTitleSuffix,
			'localeToTitleSeparator' => $localeToTitleSeparator,
			'localeToTitleFormat' => $localeToTitleFormat,
			'domainToLocale' => $domainToLocale,
			'domainToEnvironment' => $domainToEnvironment,
			'domainToProtected' => $domainIsProtected,
			'domainToScheme' => $domainToScheme,
			'domainToUseWww' => $domainToUseWww,
			'domainByEnvironment' => $domainByEnvironment,
			'domains' => $domains,
		];
	}
}

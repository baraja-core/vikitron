<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Translation;


use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Class TranslatorHelper decorates Translator
 *
 * @package Mathematicator\Engine\Translation
 */
final class TranslatorHelper
{

	/** @var Translator */
	public $translator;


	/**
	 * Available translations ordered by priority
	 *
	 * @var string[]
	 */
	private $languages = ['cs_CZ', 'en_US'];


	/**
	 * If no translation is available, than this languages are used instead.
	 *
	 * @var string[]
	 */
	private $fallbackLanguages = ['en_US', 'cs_CZ'];


	public function __construct()
	{
		$this->translator = new Translator($this->languages[0]);

		$this->translator->addLoader('yaml', new YamlFileLoader());

		$this->translator->setFallbackLocales($this->fallbackLanguages);
		$this->translator->setLocale($this->languages[0]);
	}


	/**
	 * @param string $dir Directory path
	 * @param string $domain Translation file prefix (without trailing dot)
	 * @param string $suffix Translation file suffix (without leading dot)
	 * @param string $format Data format in translation file
	 */
	public function addResource($dir, $domain = null, $suffix = 'yml', $format = 'yaml'): void
	{
		foreach ($this->languages as $languageCode) {
			$this->translator->addResource(
				$format,
				$dir . '/' . ($domain ?: 'messages') . '.' . $languageCode . '.' . $suffix,
				$languageCode,
				$domain
			);
		}
	}
}

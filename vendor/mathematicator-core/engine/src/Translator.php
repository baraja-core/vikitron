<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Mathematicator\Engine\Translation\TranslatorHelper;
use Nette\Localization\ITranslator;

final class Translator implements ITranslator
{

	/** @var TranslatorHelper */
	private $translatorHelper;


	public function __construct(TranslatorHelper $translatorHelper)
	{
		$this->translatorHelper = $translatorHelper;
		$this->translatorHelper->addResource(__DIR__ . '/../translations', 'engine');
	}


	/**
	 * @param mixed $message
	 * @param mixed ...$parameters
	 * @return string
	 */
	public function translate($message, ...$parameters): string
	{
		$translatorParams = [];
		if (isset($parameters[0]) && is_array($parameters[0])) {
			// Process named parameters
			foreach ($parameters[0] as $paramKey => $paramValue) {
				$translatorParams['%' . $paramKey . '%'] = $paramValue;
			}
		} else {
			// Process sequential parameters
			for ($i = 0; isset($parameters[$i]); $i++) {
				$translatorParams['%' . $i . '%'] = $parameters[$i];
			}
		}

		$domain = explode('.', $message)[0];

		return $this->translatorHelper->translator->trans(
			mb_substr($message, mb_strlen($domain) + 1),
			$translatorParams,
			$domain
		);
	}
}

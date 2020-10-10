<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step;


use Mathematicator\Engine\Step\Step;

final class StepFactory
{
	public static function addStep(?string $title = null, ?string $latex = null, ?string $description = null): Step
	{
		return new Step($title, $latex, $description);
	}


	/**
	 * Generate URL to API endpoint in format `/api/v1/mathematicator-engine/search-step`.
	 * In new version all steps will be sent as array of json objects.
	 *
	 * @param string $controllerClass implements IStepController
	 * @param mixed[] $data
	 * @return string
	 */
	public static function getAjaxEndpoint(string $controllerClass, array $data): string
	{
		return self::getBaseUrl() . '/api/v1/mathematicator-engine/search-step?controller=' . urlencode((string) $controllerClass) . '&data=' . urlencode((string) \json_encode($data));
	}


	private static function getBaseUrl(bool $useCache = true): ?string
	{
		static $return;

		if ($useCache === true && $return !== null) {
			return $return;
		}
		if (($currentUrl = self::getCurrentUrl()) !== null) {
			if (preg_match('/^(https?:\/\/.+)\/www\//', $currentUrl, $localUrlParser)) {
				$return = $localUrlParser[0];
			} elseif (preg_match('/^(https?:\/\/[^\/]+)/', $currentUrl, $publicUrlParser)) {
				$return = $publicUrlParser[1];
			}
		}
		if ($return !== null) {
			$return = rtrim($return, '/');
		}

		return $return;
	}


	/**
	 * Return current absolute URL.
	 * Return null, if current URL does not exist (for example in CLI mode).
	 */
	private static function getCurrentUrl(): ?string
	{
		if (!isset($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'])) {
			return null;
		}

		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
			. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}

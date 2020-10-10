<?php

declare(strict_types=1);

namespace Baraja\Localization;


final class Translation
{

	/** @var string[]|null */
	private $storage;

	/** @var string[]|null */
	private $startupState;


	/**
	 * @param string|null $data
	 * @param string|null $language
	 * @throws LocalizationException
	 */
	public function __construct(?string $data, string $language = null)
	{
		if ($data !== null && strncmp($data, 'T:{', 3) === 0) {
			$json = (string) preg_replace(
				'/^T:/', '',
				str_replace("\n", '\n',
					str_replace(["\r\n", "\r"], "\n", $data)
				)
			);
			$flags = JSON_BIGINT_AS_STRING;

			if (PHP_VERSION_ID < 70000) {
				if ($json === '') {
					throw new LocalizationException('Syntax error:' . "\nJson: " . $json . "\n\nOriginal data:\n" . $data);
				}

				if (\defined('JSON_C_VERSION') && !preg_match('##u', $json)) {
					throw new LocalizationException('Invalid UTF-8 sequence:' . "\n" . $data, 5);
				}

				if (\defined('JSON_C_VERSION') && PHP_INT_SIZE === 8) {
					$flags &= ~JSON_BIGINT_AS_STRING; // not implemented in PECL JSON-C 1.3.2 for 64bit systems
				}
			}

			$storageData = json_decode($json, true, 512, $flags);
			if ($error = json_last_error()) {
				throw new LocalizationException(
					json_last_error_msg() . "\nJson: " . $json . "\n\nOriginal data:\n" . $data,
					$error
				);
			}

			$this->startupState = $this->storage = $storageData;
		} else {
			if ($language === null) {
				if (PHP_SAPI === 'cli') {
					$language = LocalizationHelper::getLocalization()->getDefaultLocale();
				} else {
					$language = LocalizationHelper::getLocale(true);
				}
			}

			$this->storage[$language] = $data;
		}
	}


	public function __toString(): string
	{
		return $this->getTranslation() ?? '';
	}


	/**
	 * Return best translation. If language is null, use current language by automatic detection.
	 *
	 * @param string|null $language
	 * @param bool $fallback
	 * @return string|null
	 */
	public function getTranslation(string $language = null, bool $fallback = true): ?string
	{
		if ($language === null) {
			$language = LocalizationHelper::getLocale(true);
		}

		if (\is_array($this->storage) === false) {
			return '#INVALID_DATA#';
		}

		if (isset($this->storage[$language]) === true) {
			return $this->storage[$language];
		}

		if ($fallback === true) {
			if (isset(($fallbackLanguages = LocalizationHelper::getFallbackLocales())[$language]) === true) {
				foreach ($fallbackLanguages[$language] as $fallbackLanguage) {
					if (isset($this->storage[$fallbackLanguage]) === true) {
						return $this->storage[$fallbackLanguage];
					}
				}
			}

			if (\is_array($this->storage) === true) {
				return $this->storage[array_keys($this->storage)[0]];
			}
		}

		return '#NO_DATA#';
	}


	public function addTranslate(?string $haystack, string $language = null): bool
	{
		if ($language === null) {
			$language = LocalizationHelper::getLocale(true);
		}

		if (isset($this->storage[$language]) === true && $this->storage[$language] === $haystack) {
			return false;
		}

		$this->storage[$language] = $haystack;

		return true;
	}


	/**
	 * Serialize translate object to save in database.
	 *
	 * @internal
	 * @return string
	 */
	public function getSerialize(): string
	{
		$json = json_encode(
			$this->storage,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			| (\defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0)
		);

		if (PHP_VERSION_ID < 70100) {
			$json = str_replace(["\xe2\x80\xa8", "\xe2\x80\xa9"], ['\u2028', '\u2029'], $json);
		}

		return 'T:' . $json;
	}


	/**
	 * @return string[]|null
	 */
	public function getStartupState(): ?array
	{
		return $this->startupState;
	}


	/**
	 * @return string[]|null
	 */
	public function getStorage(): ?array
	{
		return $this->storage;
	}


	/**
	 * @internal
	 * @return Translation
	 */
	public function regenerate(): self
	{
		return new self($this->getSerialize());
	}
}

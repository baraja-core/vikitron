<?php

declare(strict_types=1);

namespace Baraja\Localization;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TranslateType extends Type
{
	public const TRANSLATE_TYPE = 'translate';


	/**
	 * @param mixed[] $fieldDeclaration
	 * @param AbstractPlatform $platform
	 * @return string
	 */
	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
	{
		return $platform->getClobTypeDeclarationSQL($fieldDeclaration);
	}


	/**
	 * @param string $value
	 * @param AbstractPlatform $platform
	 * @return Translation
	 * @throws LocalizationException
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform): Translation
	{
		return new Translation($value);
	}


	/**
	 * @param Translation|string|mixed|null $value
	 * @param AbstractPlatform $platform
	 * @return string|null
	 * @throws LocalizationException
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
	{
		if ($value === null) {
			return null;
		}

		if ($value instanceof Translation) {
			return $value->getSerialize();
		}

		if (\is_string($value) === true) {
			return (new Translation($value))->getSerialize();
		}

		throw new LocalizationException('Language data must be Translation entity, but type "' . \gettype($value) . '" given.');
	}


	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::TRANSLATE_TYPE;
	}


	/**
	 * @param AbstractPlatform $platform
	 * @return bool
	 */
	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}
}
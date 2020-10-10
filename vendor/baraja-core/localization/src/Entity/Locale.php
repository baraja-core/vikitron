<?php

declare(strict_types=1);

namespace Baraja\Localization;


use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\SmartObject;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *    name="core__localization_locale",
 *    indexes={
 *       @Index(name="locale__locale_id", columns={"locale", "id"}),
 *       @Index(name="locale__active", columns={"active"})
 *    }
 * )
 */
class Locale
{
	use UuidIdentifier;
	use SmartObject;

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true, length=2)
	 */
	private $locale;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $active = true;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean", name="`is_default`")
	 */
	private $default = false;

	/**
	 * @var int
	 * @ORM\Column(type="smallint")
	 */
	private $position = 1;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $insertedDate;

	/**
	 * @var Domain[]|Collection
	 * @ORM\OneToMany(targetEntity="Domain", mappedBy="locale")
	 */
	private $domains;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=64, nullable=true)
	 */
	private $titleSuffix;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=8, nullable=true)
	 */
	private $titleSeparator;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=64, nullable=true)
	 */
	private $titleFormat;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=64, nullable=true)
	 */
	private $siteName;


	public function __construct(string $locale)
	{
		if (!preg_match('/^[a-z]{2}$/', $locale = strtolower(trim($locale)))) {
			throw new \InvalidArgumentException('Locale "' . $locale . '" must be 2 [a-z] characters.');
		}

		$this->locale = $locale;
		$this->insertedDate = DateTime::from('now');
		$this->domains = new ArrayCollection;
	}


	public function __toString(): string
	{
		return $this->getLocale();
	}


	public function getLocale(): string
	{
		return $this->locale;
	}


	public function isActive(): bool
	{
		return $this->active;
	}


	public function setActive(bool $active = true): void
	{
		$this->active = $active;
	}


	public function isDefault(): bool
	{
		return $this->default;
	}


	public function setDefault(bool $default): void
	{
		$this->default = $default;
	}


	public function getPosition(): int
	{
		return $this->position;
	}


	public function setPosition(int $position): void
	{
		if ($position < 0) {
			$position = 0;
		}
		if ($position > 32767) {
			$position = 32767;
		}

		$this->position = $position;
	}


	public function getInsertedDate(): \DateTime
	{
		return $this->insertedDate;
	}


	public function getTitleSuffix(): ?string
	{
		return $this->titleSuffix;
	}


	public function setTitleSuffix(?string $titleSuffix): void
	{
		if ($titleSuffix !== null && Strings::length($titleSuffix) > 64) {
			throw new \InvalidArgumentException('The maximum length of the title suffix is 64 characters, but "' . $titleSuffix . '" given.');
		}

		$this->titleSuffix = trim($titleSuffix ?? '') ?: null;
	}


	public function getTitleSeparator(): ?string
	{
		return $this->titleSeparator;
	}


	public function setTitleSeparator(?string $titleSeparator): void
	{
		if ($titleSeparator !== null && Strings::length($titleSeparator) > 8) {
			throw new \InvalidArgumentException('The maximum length of the title separator is 8 characters, but "' . $titleSeparator . '" given.');
		}

		$this->titleSeparator = trim($titleSeparator ?? '') ?: null;
	}


	public function getTitleFormat(): ?string
	{
		return $this->titleFormat;
	}


	public function setTitleFormat(?string $titleFormat): void
	{
		if ($titleFormat !== null && Strings::length($titleFormat) > 64) {
			throw new \InvalidArgumentException('The maximum length of the title format is 64 characters, but "' . $titleFormat . '" given.');
		}

		$this->titleFormat = trim($titleFormat ?? '') ?: null;
	}


	public function getSiteName(): ?string
	{
		return $this->siteName;
	}


	public function setSiteName(?string $siteName): void
	{
		if ($siteName !== null && Strings::length($siteName) > 64) {
			throw new \InvalidArgumentException('The maximum length of the site name is 64 characters, but "' . $siteName . '" given.');
		}

		$this->siteName = trim($siteName ?? '') ?: null;
	}
}

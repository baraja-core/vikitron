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
	 * @ORM\Column(type="integer")
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
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $titleSuffix;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=8, nullable=true)
	 */
	private $titleSeparator;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $titleFormat;


	/**
	 * @param string $locale
	 */
	public function __construct(string $locale)
	{
		$this->locale = strtolower($locale);
		$this->insertedDate = DateTime::from('now');
		$this->domains = new ArrayCollection;
	}


	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->getLocale();
	}


	/**
	 * @return string
	 */
	public function getLocale(): string
	{
		return $this->locale;
	}


	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}


	/**
	 * @param bool $active
	 */
	public function setActive(bool $active = true): void
	{
		$this->active = $active;
	}


	/**
	 * @return bool
	 */
	public function isDefault(): bool
	{
		return $this->default;
	}


	/**
	 * @param bool $default
	 */
	public function setDefault(bool $default): void
	{
		$this->default = $default;
	}


	/**
	 * @return int
	 */
	public function getPosition(): int
	{
		return $this->position;
	}


	/**
	 * @param int $position
	 */
	public function setPosition(int $position): void
	{
		$this->position = $position;
	}


	/**
	 * @return \DateTime
	 */
	public function getInsertedDate(): \DateTime
	{
		return $this->insertedDate;
	}


	/**
	 * @return string|null
	 */
	public function getTitleSuffix(): ?string
	{
		return $this->titleSuffix;
	}


	/**
	 * @param string|null $titleSuffix
	 */
	public function setTitleSuffix(?string $titleSuffix): void
	{
		$this->titleSuffix = $titleSuffix;
	}


	/**
	 * @return string|null
	 */
	public function getTitleSeparator(): ?string
	{
		return $this->titleSeparator;
	}


	/**
	 * @param string|null $titleSeparator
	 */
	public function setTitleSeparator(?string $titleSeparator): void
	{
		$this->titleSeparator = $titleSeparator;
	}


	/**
	 * @return string|null
	 */
	public function getTitleFormat(): ?string
	{
		return $this->titleFormat;
	}


	/**
	 * @param string|null $titleFormat
	 */
	public function setTitleFormat(?string $titleFormat): void
	{
		$this->titleFormat = $titleFormat;
	}
}

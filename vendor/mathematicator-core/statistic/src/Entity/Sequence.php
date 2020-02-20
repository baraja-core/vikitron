<?php

declare(strict_types=1);

namespace Mathematicator\Statistics\Entity;


use Baraja\Doctrine\UUID\UuidIdentifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * @ORM\Entity()
 */
class Sequence
{

	use UuidIdentifier;
	use SmartObject;

	private const FORMAT_PATTERN = '/^\%(?<type>[a-zA-Z0-9]+)\s+(A\d+)\s?(?<content>.*?)\s*$/';

	/**
	 * @var string
	 * @ORM\Column(type="string", unique=true)
	 */
	private $aId;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $sequence;

	/**
	 * @var string|null
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $data;

	/**
	 * @param string $aId
	 */
	public function __construct(string $aId)
	{
		$this->aId = $aId;
		$this->updateData();
	}

	/**
	 * @return string
	 */
	public function getAId(): string
	{
		return $this->aId;
	}

	/**
	 * @return int[]
	 */
	public function getSequence(): array
	{
		if ($this->sequence === null) {
			return [];
		}

		$return = [];
		foreach (explode(',', $this->sequence) as $item) {
			$return[] = (int) trim($item);
		}

		return $return;
	}

	/**
	 * @return string|null
	 */
	public function getData(): ?string
	{
		return $this->data;
	}

	/**
	 * @param string $type
	 * @return string|null
	 */
	public function getDataType(string $type): ?string
	{
		if ($this->getData() === null) {
			$this->updateData();
		}

		$return = [];

		foreach (explode("\n", $this->getData()) as $line) {
			if (preg_match(self::FORMAT_PATTERN, $line, $parser) && $parser['type'] === $type) {
				$return[] = $parser['content'];
			}
		}

		if ($return === []) {
			return null;
		}

		$return = implode("\n", $return);

		return $type === 'A' ? str_replace('_', '', $return) : $return;
	}

	public function updateData(): void
	{
		if ($this->data === null) {
			$this->data = Strings::normalize(
				Strings::fixEncoding(
					file_get_contents('https://oeis.org/search?q=id:' . $this->getAId() . '&fmt=text')
				)
			);
		}
	}

	/**
	 * @return string|null
	 */
	public function getFormula(): ?string
	{
		return $this->getDataType('F');
	}

}

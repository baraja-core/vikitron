<?php

namespace Model\Math\Statistics;


use Baraja\Doctrine\EntityManager;
use Baraja\Doctrine\EntityManagerException;
use Mathematicator\Statistics\Entity\Sequence;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

class StatisticsManager
{

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $query
	 * @return int[]|float[]
	 */
	public function getNumbers(string $query): array
	{
		$numbers = [];

		$query = preg_replace('/[^0-9-.\/]/', ';', $query);
		$query = (string) preg_replace('/\;+/', ';', $query);

		foreach (explode(';', $query) as $number) {
			if (Validators::isNumeric($number)) {
				$numbers[] = $number;
			}
		}

		return $numbers;
	}

	/**
	 * @param string $data
	 * @return int[][]|float[][]
	 */
	public function getData(string $data): array
	{
		$return = [];

		foreach (explode("\n", Strings::normalize($data)) as $line) {
			$return[] = $this->getNumbers($line);
		}

		return $return;
	}

	/**
	 * @param int[]|float[] $array
	 * @return float|int|mixed
	 */
	public function getMedian(array $array)
	{
		if ($array) {
			$count = \count($array);
			sort($array);
			$mid = (int) floor(($count - 1) / 2);

			return ($array[$mid] + $array[$mid + 1 - $count % 2]) / 2;
		}

		return 0;
	}

	/**
	 * @param int[]|float[] $array
	 * @return float|int
	 */
	public function getAverage(array $array)
	{
		if ($array) {
			$sum = 0;
			$count = 0;
			foreach ($array as $item) {
				$count++;
				$sum += $item;
			}

			return $sum / ($count === 0 ? 1 : $count);
		}

		return 0;
	}

	/**
	 * @param \int[] $sequence
	 * @param int $limit
	 * @return Sequence[]
	 */
	public function getSequences(array $sequence, int $limit = 6): array
	{
		/** @var Sequence[] $return */
		$return = $this->entityManager->getRepository(Sequence::class)
			->createQueryBuilder('sequence')
			->where('sequence.sequence LIKE :sequence')
			->setParameter('sequence', implode(',', $sequence) . ',%')
			->setMaxResults($limit)
			->getQuery()
			->getResult();

		foreach ($return as $item) {
			if ($item->getData() === null) {
				$item->updateData();
				try {
					$this->entityManager->flush($item);
				} catch (EntityManagerException $e) {
				}
			}
		}

		return $return;
	}

	/**
	 * @param string $aId
	 * @return Sequence
	 */
	public function getSequence(string $aId): Sequence
	{
		/** @var Sequence $return */
		$return = $this->entityManager->getRepository(Sequence::class)
			->createQueryBuilder('sequence')
			->where('sequence.aId = :aId')
			->setParameter('aId', $aId)
			->setMaxResults(1)
			->getQuery()
			->getSingleResult();

		if ($return->getData() === null) {
			$return->updateData();
			$this->entityManager->flush($return);
		}

		return $return;
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Helper;


use Error;
use Mathematicator\Engine\Helpers;
use RuntimeException;
use function time;

final class Czech
{

	/** @throws Error */
	public function __construct()
	{
		throw new Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * Format number and string by count of items by czech grammar.
	 *
	 * inflection($count, ['zájezd', 'zájezdy', 'zájezdů']) => 1 zájezd, 3 zájezdy, 24 zájezdů
	 *
	 * @param string[] $parameters
	 * @return string
	 */
	public static function inflection(int $number, array $parameters): string
	{
		$numberTxt = number_format($number, 0, '.', ' ');
		$parameters = Helpers::strictScalarType($parameters);

		if (!isset($parameters[0], $parameters[1], $parameters[2])) {
			throw new RuntimeException('Parameter [0, 1, 2] does not set. Given: ["' . implode('", "', $parameters) . '"].');
		}

		[$for1, $for234, $forOthers] = $parameters;

		$absNumber = abs($number);
		if ($absNumber === 1) {
			$result = $for1;
		} elseif ($absNumber >= 2 && $absNumber <= 4) {
			$result = $for234;
		} else {
			$result = $forOthers;
		}

		return $numberTxt . ' ' . $result;
	}


	/**
	 * Return "6. září 2018"
	 *
	 * @param string|int|\DateTime $date
	 * @param bool $singular (true => "5. květen 2018", false => "5. května 2018")
	 * @return string
	 */
	public static function getDate($date = null, bool $singular = false): string
	{
		if ($date === null) {
			$time = time();
		} elseif ($date instanceof \DateTime) {
			$time = $date->getTimestamp();
		} else {
			$time = is_numeric($date) ? $date : @strtotime($date);
		}

		$months = [
			'ledna', 'února', 'března', 'dubna', 'května',
			'června', 'července', 'srpna', 'září', 'října',
			'listopadu', 'prosince',
		];

		$singularMonths = [
			'leden', 'únor', 'březen', 'duben', 'květen',
			'červen', 'červenec', 'srpen', 'září', 'říjen',
			'listopad', 'prosinec',
		];

		[$day, $month, $year] = explode('-', date('j-n-Y', (int) $time));

		return $day . '. ' . ($singular === true
				? $singularMonths[(int) $month - 1]
				: $months[(int) $month - 1]
			) . ' ' . $year;
	}
}

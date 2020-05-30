<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Helper;


use Mathematicator\Engine\MathematicatorException;
use Nette\StaticClass;

class Czech
{
	use StaticClass;

	/**
	 * Format number and string by count of items by czech grammar.
	 *
	 * inflection($count, ['zájezd', 'zájezdy', 'zájezdů']) => 1 zájezd, 3 zájezdy, 24 zájezdů
	 *
	 * @param int $number
	 * @param string[] $parameters
	 * @return string
	 * @throws MathematicatorException
	 */
	public static function inflection(int $number, array $parameters): string
	{
		$numberTxt = number_format($number, 0, '.', ' ');
		$parameters = Safe::strictScalarType($parameters);

		if (!isset($parameters[0], $parameters[1], $parameters[2])) {
			throw new MathematicatorException(
				'Parameter [0, 1, 2] does not set. Given: ["' . implode('", "', $parameters) . '"].'
			);
		}

		[$for1, $for234, $forOthers] = $parameters;

		if (!$number) {
			$result = '0 ' . $forOthers;
		} elseif ($number === 1) {
			$result = '1 ' . $for1;
		} elseif ($number >= 2 && $number <= 4) {
			$result = $numberTxt . ' ' . $for234;
		} else {
			$result = $numberTxt . ' ' . $forOthers;
		}

		return $result;
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
			$time = \time();
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
<?php

declare(strict_types=1);

namespace Mathematicator;


use Mathematicator\Engine\MathematicatorException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

class NumberRewriter
{

	/**
	 * @var string[]
	 */
	private $basic = [
		'nula' => 0,
		'jedna' => 1,
		'dvě' => 2,
		'dva' => 2,
		'tři' => 3,
		'čtyři' => 4,
		'pět' => 5,
		'šest' => 6,
		'sedm' => 7,
		'osm' => 8,
		'devět' => 9,
		'deset' => 10,
		'jedenáct' => 11,
		'dvanáct' => 12,
		'třináct' => 13,
		'čtrnáct' => 14,
		'patnáct' => 15,
		'šestnáct' => 16,
		'sedmnáct' => 17,
		'osmnáct' => 18,
		'devatenáct' => 19,
		'dvacet' => 20,
	];

	/**
	 * @var string[]
	 */
	private $teens = [
		1 => 'deset', 'dvacet', 'třicet', 'čtyřicet', 'padesát', 'šedesát', 'sedmdesát', 'osmdesát', 'devadesát', 'sto',
	];

	/**
	 * @var string[]
	 */
	private $hundreds = [
		1 => 'sto', 'dvě stě', 'tři sta', 'čtyři sta', 'pět set', 'šest set', 'sedm set', 'osm set', 'devět set', 'tisíc',
	];

	/**
	 * @var string[][]
	 */
	private $levels = [
		0 => ['', '', ''],
		3 => ['tisíc', 'tisíce', 'tisíc'],
		6 => ['milion', 'miliony', 'milionů'],
		9 => ['miliarda', 'miliardy', 'miliard'],
		12 => ['bilion', 'biliony', 'bilionù'],
		15 => ['biliarda', 'biliardy', 'biliard'],
		18 => ['trilion', 'triliony', 'trilionů'],
		21 => ['triliarda', 'triliardy', 'triliard'],
		24 => ['kvadrilion', 'kvadriliony', 'kvadrilionů'],
		30 => ['kvintilion', 'kvintiliony', 'kvintilionů'],
		36 => ['sextilion', 'sextiliony', 'sextilionů'],
		42 => ['septilion', 'septiliony', 'septilionů'],
		48 => ['oktilion', 'oktiliony', 'oktilionů'],
		54 => ['nonilion', 'noniliony', 'nonilionů'],
		60 => ['decilion', 'deciliony', 'decilionů'],
		66 => ['undecilion', 'undeciliony', 'undecilionů'],
		72 => ['duodecilion', 'duodeciliony', 'duodecilionů'],
		78 => ['tredecilion', 'tredeciliony', 'tredecilionů'],
		84 => ['kvatrodecilion', 'kvatrodeciliony', 'kvatrodecilionů'],
		90 => ['kvindecilion', 'kvindeciliony', 'kvindecilionů'],
		96 => ['sexdecilion', 'sexdeciliony', 'sexdecilionů'],
		102 => ['septendecilion', 'septendeciliony', 'septendecilionů'],
		108 => ['oktodecilion', 'oktodeciliony', 'oktodecilionů'],
		114 => ['novemdecilion', 'novemdeciliony', 'novemdecilionů'],
		120 => ['vigintilion', 'vigintiliony', 'vigintilionů'],
		192 => ['duotrigintilion', 'duotrigintiliony', 'duotrigintilionů'],
		600 => ['centilion', 'centiliony', 'centilionů'],
	];

	/**
	 * @var string[][]
	 */
	private $fractions = [
		1 => ['jednina'],
		2 => ['polovina', 'poloviny', 'polovin'],
		3 => ['třetina', 'třetiny', 'třetin'],
		4 => ['čtvrtina', 'čtvrtiny', 'čtvrtin'],
		5 => ['pětina', 'pětiny', 'pětin'],
		6 => ['šestina', 'šestiny', 'šestin'],
		7 => ['sedmina', 'sedminy', 'sedmin'],
		8 => ['osmina', 'osminy', 'osmin'],
		9 => ['devítina', 'devítiny', 'devítin'],
		10 => ['desetina', 'desetiny', 'desetin'],
	];

	/**
	 * @var string[]
	 */
	private $regex = [
		'^(m[ií]nus)\s*(.+)$' => '-$2',
		'(^|[^\d])(\d+)-ti(\s|$)' => '$1$2$3',
		'\s*(celé|celých|celá)\s*' => '|',
	];

	// --------------------------------------------------- To number ---------------------------------------------------

	/**
	 * @param string $haystack
	 * @return string
	 */
	public function toNumber(string $haystack): string
	{
		$haystack = trim(preg_replace('/\s+/', ' ', $haystack));

		foreach ($this->regex as $key => $value) {
			$haystack = (string) preg_replace('/' . $key . '/', $value, $haystack);
		}

		$return = '';
		foreach (explode(' ', $haystack) as $word) {
			$return .= $this->processWord($word) . ' ';
		}

		return trim($return);
	}

	// ---------------------------------------------------- To word ----------------------------------------------------

	/**
	 * @param string $number
	 * @return string
	 */
	public function toWord(string $number): string
	{
		$number = trim($number);

		if (Strings::startsWith($number, '-')) {
			return 'minus ' . $this->toWord(preg_replace('/^-/', '', $number));
		}

		if (!Validators::isNumericInt($number)) {
			return $this->toWordFloat($number);
		}

		foreach ($this->basic as $w => $n) {
			if ($n === $number) {
				return (string) $w;
			}
		}

		$return = '';
		$tokens = [];

		while (true) {
			if (preg_match('/^(.+)(\d{3})$/', $number, $numberParser)) {
				$number = $numberParser[1];
				$tokens[] = $numberParser[2];
			} else {
				$tokens[] = $number;
				break;
			}
		}

		$tokens = array_reverse($tokens);
		$level = (count($tokens) - 1) * 3;

		foreach ($tokens as $token) {
			$levelName = (isset($this->levels[$level])
				? $this->smartInflect($token, $this->levels[$level][0], $this->levels[$level][1], $this->levels[$level][2]) . ' '
				: ''
			);

			if ($token !== '1' && $levelName !== '') {
				$return .= $this->toWordTrinity($token) . ' ';
			}

			$return .= $levelName;

			$level -= 3;
		}

		return trim(preg_replace('/\s+/', ' ', $return));
	}

	/**
	 * @param string $float
	 * @return string
	 */
	private function toWordFloat(string $float): string
	{
		return $float;
	}

	/**
	 * @param string $number
	 * @return string
	 * @throws \Exception
	 */
	private function toWordTrinity(string $number): string
	{
		$number = strrev(ltrim($number, '0'));
		$return = '';

		if (\strlen($number) > 3) {
			throw new MathematicatorException('Number length must be 1-3 chars.');
		}

		// hundreds
		if (isset($number[2]) && $number[2] !== '0') {
			$hundreds = (int) ($number[2]);
			$return = $this->hundreds[$hundreds] . ' ';
		}

		// teens
		if (isset($number[1])) {
			if ($number[1] === '1') {
				$return .= $this->toWord($number[1] . $number[0]);
			} elseif ($number[1] !== '0') {
				$teens = (int) ($number[1]);
				$return .= $this->teens[$teens] . ' ';

				// ones
				if (isset($number[0]) && $number[0] !== '0') {
					$return .= $this->formatOnes($number[0]);
				}
			}
		} elseif (isset($number[0]) && $number[0] !== '0') {
			$return .= $this->formatOnes($number[0]);
		}

		return trim($return);
	}

	/**
	 * @param int $number
	 * @return string
	 */
	private function formatOnes(int $number): string
	{
		return [1 => 'jedna', 'dva', 'tři', 'čtyři', 'pět', 'šest', 'sedm', 'osm', 'devět'][$number];
	}

	/**
	 * @param int $number
	 * @param string $for1
	 * @param string $for234
	 * @param string $forOther
	 * @return string
	 */
	private function smartInflect(int $number, string $for1, string $for234, string $forOther): string
	{
		if ($number === 1) {
			return $for1;
		}

		if ($number > 1 && $number < 5) {
			return $for234;
		}

		return $forOther;
	}

	/**
	 * @param string $word
	 * @return string
	 */
	private function processWord(string $word): string
	{
		foreach ($this->basic as $basicWord => $basicRewrite) {
			if ($basicWord === $word) {
				return (string) $basicRewrite;
			}
		}

		return $word;
	}

}
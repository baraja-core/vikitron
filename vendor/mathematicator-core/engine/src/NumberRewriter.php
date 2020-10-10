<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


final class NumberRewriter
{

	/** @var int[] */
	private static $basic = [
		'nula' => 0, 'jedna' => 1, 'dvě' => 2, 'dva' => 2, 'tři' => 3, 'čtyři' => 4, 'pět' => 5,
		'šest' => 6, 'sedm' => 7, 'osm' => 8, 'devět' => 9, 'deset' => 10, 'jedenáct' => 11, 'dvanáct' => 12,
		'třináct' => 13, 'čtrnáct' => 14, 'patnáct' => 15, 'šestnáct' => 16, 'sedmnáct' => 17, 'osmnáct' => 18,
		'devatenáct' => 19, 'dvacet' => 20,
	];

	/** @var string[] */
	private static $regex = [
		'^(m[ií]nus)\s*(.+)$' => '-$2',
		'(^|[^\d])(\d+)-ti(\s|$)' => '$1$2$3',
		'\s*(celé|celých|celá)\s*' => '|',
	];

	/** @var string[] */
	private static $teens = [
		1 => 'deset', 'dvacet', 'třicet', 'čtyřicet', 'padesát', 'šedesát', 'sedmdesát', 'osmdesát', 'devadesát', 'sto',
	];

	/** @var string[] */
	private static $hundreds = [
		1 => 'sto', 'dvě stě', 'tři sta', 'čtyři sta', 'pět set', 'šest set', 'sedm set', 'osm set', 'devět set', 'tisíc',
	];

	/** @var string[][] */
	private static $levels = [
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

	/** @var string[][] */
	private static $fractions = [
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


	public function toNumber(string $haystack): string
	{
		$haystack = trim((string) preg_replace('/\s+/', ' ', $haystack));

		foreach (self::$regex as $key => $value) {
			$haystack = (string) preg_replace('/' . $key . '/', $value, $haystack);
		}

		$return = '';
		foreach (explode(' ', $haystack) as $word) {
			$return .= $this->processWord($word) . ' ';
		}

		return trim($return);
	}


	private function processWord(string $word): string
	{
		foreach (self::$basic as $basicWord => $basicRewrite) {
			if ($basicWord === $word) {
				return (string) $basicRewrite;
			}
		}

		return $word;
	}
}

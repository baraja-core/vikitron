<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Nette\Utils\Strings;

final class QueryNormalizer
{

	/** @var string[] */
	private static $regexMap = [
		'\s*(\#|\/\/).*$' => '',
		'(\d)([,;])\s+(\d)' => '$1$2$3',
		'(\d)\s+(\d)' => '$1$2',
		'(\d)(\*{2,}|\˘)(\d)' => '$1^$3',
		'(\d)\s*(\:|÷)\s*(\d)' => '$1/$3',
		'(^|[^\d\,])(\d+)\,((?:\d{3}\s*)+)($|[^\d\,])' => '$1$2$3$4',
		'(^|[^\d\,])(\d+)\,(\d+)($|[^\d\,])' => '$1$2.$3$4',
		'(\d)\s+(\p{L})([^\p{L}]|$)' => '$1$2$3',
		'\+\-' => '-',
		'(\d+)\s*plus\s*(\d+)' => '$1+$2',
		'(\d+)\s*(krát|krat)\s*(\d+)' => '$1*$3',
		'\s*([\+\-\*\/\^])\s*' => '$1',
		'(\d)(\p{L})([^\p{L}]|$)' => '$1*$2$3',
		'(nekonecno|nekonečno|infty|infinity|[iI][nN][fF]|∞)' => 'INF',
		'(π|(?:\\\\)?[pP][iíI])' => 'PI',
		'\\\\frac{([^{}]+)}{([^{}]+)}' => '($1)/($2)',
		'([^\p{L}]|^)\((-?[\d\.]+)\)' => '$1$2',
		'\s*\=\s*' => '=',
		'×' => '*',
		'[–−]+' => '-',
		'(\d)\(' => '$1*(',
		'([√√]|odmocnina|odm|sqrt)(?:\s(?:ze|z))?\s*\(?' => 'sqrt(',
		'\)\(' => ')*(',
		'\|([^|]+)\|' => 'abs($1)',
		'\s*(vs\.?)\s*' => ' $1 ',
		'[,;]\s*\.\.\.+$' => '',
		'[\.,;:]$' => '',
		'\(\s(\d)' => '($1',
		'\?$' => '',
	];

	/** @var NumberRewriter */
	private $numberRewriter;


	public function __construct()
	{
		$this->numberRewriter = new NumberRewriter;
	}


	/**
	 * Magic convertor of user input to normalized machine-readable form.
	 */
	public function normalize(string $query): string
	{
		$query = trim(Strings::normalize(Strings::fixEncoding($query)), " \t\n\r\"'");
		$query = $this->removeEmoji($query);
		$query = (string) preg_replace('/=\??$/', '', $query);

		$return = '';
		foreach (explode('=', $query) as $part) {
			$part = trim($part);
			$part = $this->taskFixBrackets($part);
			$part = $this->taskRewriteWordNumber($part);
			$part = $this->taskNormalizeNumber($part);
			$part = $this->taskRegexReplaceMap($part);
			$return .= ($return !== '' ? '=' : '') . $part;
		}

		$return = $this->replaceSpecialCharacters($return);
		$return = (string) preg_replace('/\s+/', ' ', $return);

		return trim($return);
	}


	/**
	 * If the number of left and right brackets in the expression does not match, a correction is made.
	 *
	 * Cases:
	 *    1. Number of brackets is same, for ex. "3*(5+1)".
	 *    2. Number of left brackets is more, for ex. "3*(5+1".
	 *    3. Number of right brackets is more, for ex. "5+1)+2".
	 *    4. The outer brackets are redundant, for ex. "(((1+2)))".
	 */
	private function taskFixBrackets(string $query): string
	{
		if (($leftCount = substr_count($query, '(')) === ($rightCount = substr_count($query, ')'))) { // 1.
			$return = $query;
		} elseif ($leftCount > $rightCount) { // 2.
			$return = $query . str_repeat(')', $leftCount - $rightCount);
		} else { // 3.
			$return = str_repeat('(', $rightCount - $leftCount) . $query;
		}

		return $this->removeRedundantBrackets($return); // 4.
	}


	private function taskRegexReplaceMap(string $query): string
	{
		while (true) {
			$oldQuery = $query;

			foreach (self::$regexMap as $regex => $replace) {
				$query = (string) preg_replace('/' . $regex . '/', $replace, $query);
			}

			if ($oldQuery === $query) {
				break;
			}
		}

		return $query;
	}


	private function taskNormalizeNumber(string $query): string
	{
		return (string) preg_replace_callback('/([\d\,]+\,\d{3})\.(\d+)/', static function (array $match): string {
			return preg_replace('/\D/', '', $match[1]) . '.' . $match[2];
		}, $query);
	}


	private function taskRewriteWordNumber(string $query): string
	{
		return $this->numberRewriter->toNumber($query);
	}


	private function removeEmoji(string $query): string
	{
		// Match Emoticons
		$query = (string) preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $query);

		// Match Miscellaneous Symbols and Pictographs
		$query = (string) preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $query);

		// Match Transport And Map Symbols
		$query = (string) preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $query);

		// Match Miscellaneous Symbols
		$query = (string) preg_replace('/[\x{2600}-\x{26FF}]/u', '', $query);

		// Match Dingbats
		$query = (string) preg_replace('/[\x{2700}-\x{27BF}]/u', '', $query);

		return $query;
	}


	private function replaceSpecialCharacters(string $query): string
	{
		$query = str_replace(['½', 'Ã'], [' 1/2', 'á'], $query);

		return $query;
	}


	private function removeRedundantBrackets(string $haystack): string
	{
		$returnInner = $haystack;
		while (true) {
			if (preg_match('/^\((?<content>.+)\)$/', $returnInner, $bracketParser)) {
				if (preg_match('/^\(([^)(]+)\)$/', $returnInner)) {
					$haystack = (string) preg_replace('/^\(([^)(]+)\)$/', '$1', $returnInner);
				}
				$returnInner = $bracketParser['content'];
			} else {
				break;
			}
		}

		return $haystack;
	}
}

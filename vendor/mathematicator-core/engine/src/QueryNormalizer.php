<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Mathematicator\NumberRewriter;
use Nette\Utils\Strings;

class QueryNormalizer
{

	/**
	 * @var string[]
	 */
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

	/**
	 * @var NumberRewriter
	 */
	private $numberRewriter;

	/**
	 * @param NumberRewriter $numberRewriter
	 */
	public function __construct(NumberRewriter $numberRewriter)
	{
		$this->numberRewriter = $numberRewriter;
	}

	/**
	 * Magic convertor of user input to normalized machine-readable form.
	 *
	 * @param string $query
	 * @return string
	 */
	public function normalize(string $query): string
	{
		$query = Strings::trim(Strings::normalize($query));
		$query = (string) preg_replace('/\s+/', ' ', $query);
		$query = (string) preg_replace('/=\??$/', '', $query);

		$queryNew = '';
		foreach (explode('=', $query) as $queryParser) {
			$queryPart = $queryParser;
			$queryPart = $this->taskFixBrackets($queryPart);
			$queryPart = $this->taskRewriteWordNumber($queryPart);
			$queryPart = $this->taskNormalizeNumber($queryPart);
			$queryPart = $this->taskRegexReplaceMap($queryPart);
			$queryNew .= ($queryNew ? '=' : '') . $queryPart;
		}

		return $queryNew;
	}

	/**
	 * @param string $query
	 * @return string
	 */
	private function taskFixBrackets(string $query): string
	{
		$leftCount = substr_count($query, '(');
		$rightCount = substr_count($query, ')');

		if ($leftCount === $rightCount) {
			$return = $query;
		} elseif ($leftCount > $rightCount) {
			$return = $query . str_repeat(')', $leftCount - $rightCount);
		} else {
			$return = str_repeat('(', $rightCount - $leftCount) . $query;
		}

		$redundantBrackets = static function (string $return): string {
			$returnInner = $return;
			while (true) {
				if (preg_match('/^\((?<content>.+)\)$/', $returnInner, $bracketParser)) {
					if (preg_match('/^\(([^)(]+)\)$/', $returnInner)) {
						$return = preg_replace('/^\(([^)(]+)\)$/', '$1', $returnInner);
					}
					$returnInner = $bracketParser['content'];
				} else {
					break;
				}
			}

			return $return;
		};

		return $redundantBrackets($return);
	}

	/**
	 * @param string $query
	 * @return string
	 */
	private function taskRegexReplaceMap(string $query): string
	{
		while (true) {
			$oldQuery = $query;

			foreach (self::$regexMap as $regex => $replace) {
				$query = preg_replace('/' . $regex . '/', $replace, $query);
			}

			if ($oldQuery === $query) {
				break;
			}
		}

		return $query;
	}

	/**
	 * @param string $query
	 * @return string
	 */
	private function taskNormalizeNumber(string $query): string
	{
		return preg_replace_callback('/([\d\,]+\,\d{3})\.(\d+)/', function (array $match) {
			return preg_replace('/\D/', '', $match[1]) . '.' . $match[2];
		}, $query);
	}

	/**
	 * @param string $query
	 * @return string
	 */
	private function taskRewriteWordNumber(string $query): string
	{
		return $this->numberRewriter->toNumber($query);
	}

}

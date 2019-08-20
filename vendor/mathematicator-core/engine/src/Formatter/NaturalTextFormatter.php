<?php

namespace Model\Math;

use Mathematicator\Engine\QueryNormalizer;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Utils\Strings;
use Texy\Texy;

class NaturalTextFormatter
{

	/**
	 * @var Texy
	 */
	private $texy;

	/**
	 * @var QueryNormalizer
	 */
	private $queryNormalizer;

	/**
	 * @var Tokenizer
	 */
	private $tokenizer;

	/**
	 * @var string[]
	 */
	private $allowedFunctions = [
		'sin',
		'cos',
		'tan',
		'cotan',
		'tg',
		'log\d*',
		'sqrt',
	];

	public function __construct(Texy $texy, QueryNormalizer $queryNormalizer, Tokenizer $tokenizer)
	{
		$this->texy = $texy;
		$this->queryNormalizer = $queryNormalizer;
		$this->tokenizer = $tokenizer;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function formatNaturalText(string $text): string
	{
		$return = '';

		foreach (explode("\n", Strings::normalize($text)) as $line) {
			$line = trim($line);
			if ($line) {
				if (!preg_match('/^\s*https?:\/\//', $line) && !$this->containsWords($line)) {
					$rewrite = $this->queryNormalizer->normalize($line);
					$tokens = $this->tokenizer->tokenize($rewrite);
					$latex = $this->tokenizer->tokensToLatex($this->tokenizer->tokensToObject($tokens));

					$return .= '<div class="latex"><p>\(' . $latex . '\)</p><code>' . $line . '</code></div>';
				} else {
					$return .= $this->texy->process($line) . "\n\n";
				}
			}
		}

		return $return;
	}

	/**
	 * @param string $text
	 * @return bool
	 */
	private function containsWords(string $text): bool
	{
		$words = 0;

		$text = preg_replace('/\s+/', ' ', Strings::toAscii(Strings::lower($text)));

		while (true) {
			$newText = preg_replace('/([a-z0-9]{2,})\s+([a-z0-9]{1,})(\s+|[:.!?,]|$)/', '$1$2', $text);
			if ($newText === $text) {
				break;
			}
			$text = $newText;
		}

		foreach (explode(' ', $text) as $word) {
			if (preg_match('/(?<word>[a-z0-9]{3,32})/', $word, $wordParser)) {
				if ($this->wordInAllowedFunctions($wordParser['word'])) {
					continue;
				}
				if (strlen($wordParser['word']) >= 5) {
					return true;
				}
				$words++;
			}
		}

		return $words >= 3;
	}

	/**
	 * @param string $word
	 * @return bool
	 */
	private function wordInAllowedFunctions(string $word): bool
	{
		foreach ($this->allowedFunctions as $allowedFunction) {
			if (preg_match('/^' . $allowedFunction . '$/', $word)) {
				return true;
			}
		}

		return false;
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator;


use Mathematicator\Engine\QueryNormalizer;
use Mathematicator\Search\TextRenderer;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Utils\Strings;

class NaturalTextFormatter
{

	/**
	 * @var string[]
	 */
	private static $allowedFunctions = [
		'sin',
		'cos',
		'tan',
		'cotan',
		'tg',
		'log\d*',
		'sqrt',
	];

	/**
	 * @var QueryNormalizer
	 */
	private $queryNormalizer;

	/**
	 * @var Tokenizer
	 */
	private $tokenizer;

	/**
	 * @param QueryNormalizer $queryNormalizer
	 * @param Tokenizer $tokenizer
	 */
	public function __construct(QueryNormalizer $queryNormalizer, Tokenizer $tokenizer)
	{
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
					$return .= TextRenderer::process($line) . "\n\n";
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

		$text = (string) preg_replace('/\s+/', ' ', Strings::toAscii(Strings::lower($text)));

		while (true) {
			$newText = (string) preg_replace('/([a-z0-9]{2,})\s+([a-z0-9]{1,})(\s+|[:.!?,]|$)/', '$1$2', $text);
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
		foreach (self::$allowedFunctions as $allowedFunction) {
			if (preg_match('/^' . $allowedFunction . '$/', $word)) {
				return true;
			}
		}

		return false;
	}

}

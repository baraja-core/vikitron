<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Formatter;


use Mathematicator\Engine\MathFunction\FunctionManager;
use Mathematicator\Engine\QueryNormalizer;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Utils\Strings;

final class NaturalTextFormatter
{

	/** @var QueryNormalizer */
	private $queryNormalizer;

	/** @var Tokenizer */
	private $tokenizer;


	public function __construct(QueryNormalizer $queryNormalizer, Tokenizer $tokenizer)
	{
		$this->queryNormalizer = $queryNormalizer;
		$this->tokenizer = $tokenizer;
	}


	public function formatNaturalText(string $text): string
	{
		$return = '';

		foreach (explode("\n", Strings::normalize($text)) as $line) {
			if (($line = trim($line)) !== '') {
				if (!preg_match('/^\s*https?:\/\//', $line) && !$this->containsWords($line)) {
					$rewrite = $this->queryNormalizer->normalize($line);
					$tokens = $this->tokenizer->tokenize($rewrite);
					$latex = $this->tokenizer->tokensToLatex($this->tokenizer->tokensToObject($tokens));

					$return .= '<div class="latex"><p>\(' . $latex . '\)</p><code>' . $line . '</code></div>';
				} else {
					$return .= htmlspecialchars($line, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8');
				}
			}
		}

		return $return;
	}


	private function containsWords(string $haystack): bool
	{
		$words = 0;
		$haystack = (string) preg_replace('/\s+/', ' ', Strings::toAscii(Strings::lower($haystack)));

		do {
			$oldHaystack = $haystack;
			$haystack = (string) preg_replace('/([a-z0-9]{2,})\s+([a-z0-9]{1,})(\s+|[:.!?,]|$)/', '$1$2', $haystack);
		} while ($haystack !== $oldHaystack);

		foreach (explode(' ', $haystack) as $word) {
			if (preg_match('/(?<word>[a-z0-9]{3,32})/', $word, $wordParser)) {
				if ($this->wordInAllowedFunctions($wordParser['word'])) {
					continue; // never count function name as word
				}
				if (strlen($wordParser['word']) >= 5) {
					return true;
				}
				$words++;
			}
		}

		return $words >= 3;
	}


	private function wordInAllowedFunctions(string $word): bool
	{
		foreach (FunctionManager::getFunctionNames() as $allowedFunction) {
			if (preg_match('/^' . $allowedFunction . '$/', $word)) {
				return true;
			}
		}

		return false;
	}
}

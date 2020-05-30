<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Helper;


use Mathematicator\Engine\MathematicatorException;
use Nette\StaticClass;

class Terminal
{
	use StaticClass;

	/** @var int */
	private static $staticTtl = 0;


	/**
	 * Render code snippet to Terminal.
	 *
	 * @param string $path
	 * @param int|null $line -> if not null mark selected line by red color
	 */
	public static function code(string $path, int $line = null): void
	{
		echo "\n" . $path . ($line === null ? '' : ' [on line ' . $line . ']') . "\n\n";
		if (\is_file($path)) {
			echo '----- file -----' . "\n";
			$file = str_replace(["\r\n", "\r"], "\n", (string) file_get_contents($path));
			$fileParser = explode("\n", $file);
			$start = $line > 8 ? $line - 8 : 0;

			for ($i = $start; $i <= $start + 15; $i++) {
				if (!isset($fileParser[$i])) {
					break;
				}

				$currentLine = $i + 1;
				$highlight = $line === $currentLine;

				echo ($highlight ? "\e[1;37m\e[41m" : "\e[100m")
					. str_pad(' ' . $currentLine . ': ', 6, ' ') . ($highlight ? '' : "\e[0m")
					. str_replace("\t", '    ', $fileParser[$i])
					. ($highlight ? "\e[0m" : '')
					. "\n";
			}

			echo '----- file -----' . "\n\n";
		}
	}


	/**
	 * Ask question in Terminal and return user answer (string or null if empty).
	 *
	 * Function will be asked since user give valid answer.
	 *
	 * @param string $question -> only display to user
	 * @param string[]|null $possibilities -> if empty, answer can be every valid string or null.
	 * @return string|null -> null if empty answer
	 * @throws MathematicatorException
	 */
	public static function ask(string $question, ?array $possibilities = null): ?string
	{
		self::$staticTtl = 0;

		echo "\n" . str_repeat('-', 100) . "\n";

		if ($possibilities !== [] && $possibilities !== null) {
			$renderPossibilities = static function (array $possibilities): string {
				$return = '';
				$containsNull = false;

				foreach ($possibilities as $possibility) {
					if ($possibility !== null) {
						$return .= ($return === '' ? '' : '", "') . $possibility;
					} elseif ($containsNull === false) {
						$containsNull = true;
					}
				}

				return 'Possible values: "' . $return . '"' . ($containsNull ? ' or press ENTER' : '') . '.';
			};

			echo $renderPossibilities($possibilities) . "\n";
		}

		echo 'Q: ' . trim($question) . "\n" . 'A: ';

		$fOpen = fopen('php://stdin', 'rb');

		if (\is_resource($fOpen) === false) {
			throw new MathematicatorException('Problem with opening "php://stdin".');
		}

		$input = trim((string) fgets($fOpen));
		echo "\n";

		$input = $input === '' ? null : $input;

		if ($possibilities !== [] && $possibilities !== null) {
			if (\in_array($input, $possibilities, true)) {
				return $input;
			}

			self::renderError('!!! Invalid answer !!!');
			self::$staticTtl++;

			if (self::$staticTtl >= 16) {
				throw new MathematicatorException(
					'The maximum invalid response limit was exceeded. Current limit: ' . self::$staticTtl
				);
			}

			return self::ask($question, $possibilities);
		}

		return $input;
	}


	/**
	 * Render red block with error message.
	 *
	 * @param string $message
	 */
	public static function renderError(string $message): void
	{
		echo "\033[1;37m\033[41m";

		for ($i = 0; $i < 100; $i++) {
			echo ' ';
		}

		echo "\n" . str_pad('      ' . $message . '      ', 100) . "\n";

		for ($i = 0; $i < 100; $i++) {
			echo ' ';
		}

		echo "\033[0m";
	}
}
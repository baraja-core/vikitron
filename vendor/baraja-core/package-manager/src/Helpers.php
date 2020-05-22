<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


final class Helpers
{

	/**
	 * @throws \Error
	 */
	public function __construct()
	{
		throw new \Error('Class ' . get_class($this) . ' is static and cannot be instantiated.');
	}


	/**
	 * Merge right set to left set recursively.
	 *
	 * @param mixed[] $left
	 * @param mixed[] $right
	 * @return mixed[]
	 */
	public static function recursiveMerge(array &$left, array &$right): array
	{
		$return = $left;

		foreach ($right as $key => &$value) {
			if (\is_array($value) && isset($return[$key]) && \is_array($return[$key])) {
				$return[$key] = self::recursiveMerge($return[$key], $value);
			} elseif (\is_int($key)) {
				$return[] = $value;
			} else {
				$return[$key] = $value;
			}
		}

		return $return;
	}


	/**
	 * @param string $functionName
	 * @return bool
	 */
	public static function functionIsAvailable(string $functionName): bool
	{
		static $disabled;

		if (\function_exists($functionName)) {
			if ($disabled === null && \is_string($disableFunctions = ini_get('disable_functions'))) {
				$disabled = explode(',', $disableFunctions) ?: [];
			}

			return \in_array($functionName, $disabled, true) === false;
		}

		return false;
	}


	/**
	 * Render code snippet to Terminal.
	 *
	 * @param string $path
	 * @param int|null $line -> if not null mark selected line by red color
	 */
	public static function terminalRenderCode(string $path, int $line = null): void
	{
		echo "\n" . $path . ($line === null ? '' : ' [on line ' . $line . ']') . "\n\n";
		if (\is_file($path) === true) {
			echo '----- file -----' . "\n";
			$fileParser = explode("\n", str_replace(["\r\n", "\r"], "\n", (string) file_get_contents($path)));

			for ($i = ($start = $line > 8 ? $line - 8 : 0); $i <= $start + 15; $i++) {
				if (isset($fileParser[$i]) === false) {
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
	 * @throws \RuntimeException
	 */
	public static function terminalInteractiveAsk(string $question, ?array $possibilities = null): ?string
	{
		static $staticTtl = 0;

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
			throw new \RuntimeException('Problem with opening "php://stdin".');
		}

		$input = ($input = trim((string) fgets($fOpen))) === '' ? null : $input;

		if ($possibilities !== [] && $possibilities !== null) {
			if (\in_array($input, $possibilities, true)) {
				return $input;
			}

			self::terminalRenderError('!!! Invalid answer !!!');
			$staticTtl++;

			if ($staticTtl > 16) {
				throw new \RuntimeException(
					'The maximum invalid response limit was exceeded. Current limit: ' . $staticTtl
				);
			}

			return self::terminalInteractiveAsk($question, $possibilities);
		}

		return $input;
	}


	/**
	 * Render red block with error message.
	 *
	 * @param string $message
	 */
	public static function terminalRenderError(string $message): void
	{
		echo "\033[1;37m\033[41m" . str_repeat(' ', 100) . "\n";

		foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $message)) as $line) {
			while (true) {
				if (preg_match('/^(.{85,}?)[\s\n](.*)$/', $line, $match) === 0) {
					echo self::formatTerminalLine($line);
					break;
				}

				$line = $match[2];
				echo self::formatTerminalLine($match[1]);
			}
		}

		echo str_repeat(' ', 100) . "\033[0m";
	}


	/**
	 * Returns number of characters (not bytes) in UTF-8 string.
	 * That is the number of Unicode code points which may differ from the number of graphemes.
	 *
	 * @param string $s
	 * @return int
	 */
	private static function length(string $s): int
	{
		return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen(utf8_decode($s));
	}


	/**
	 * @param string $line
	 * @return string
	 */
	private static function formatTerminalLine(string $line): string
	{
		return '      ' . $line . (($repeat = 88 - self::length($line)) > 0 ? str_repeat(' ', $repeat) : '') . '      ' . "\n";
	}
}

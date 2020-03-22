<?php

declare(strict_types=1);

namespace Baraja\PackageManager;


/**
 * Find all classes in specific directory.
 * Based on https://github.com/symfony/class-loader/blob/master/ClassMapGenerator.php
 */
class ClassMapGenerator
{

	/**
	 * Iterate over all files in the given directory searching for classes.
	 *
	 * @param string $dir The directory to search in or an iterator
	 * @return string[]
	 */
	public static function createMap(string $dir): array
	{
		$map = [];

		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $file) {
			if ($file->isFile() === false) {
				continue;
			}

			if ('php' !== pathinfo($path = $file->getRealPath() ? : $file->getPathname(), PATHINFO_EXTENSION)) {
				continue;
			}

			$classes = self::findClasses($path);

			if (\PHP_VERSION_ID >= 70000) {
				// PHP 7 memory manager will not release after token_get_all(), see https://bugs.php.net/70098
				gc_mem_caches();
			}

			foreach ($classes as $class) {
				$map[$class] = $path;
			}
		}

		return $map;
	}

	/**
	 * Extract the classes in the given file.
	 *
	 * @param string $path The file to check
	 * @return string[] The found classes
	 */
	private static function findClasses(string $path): array
	{
		$contents = file_get_contents($path);
		$tokens = token_get_all($contents);
		$classes = [];
		$namespace = '';

		for ($i = 0; isset($tokens[$i]); ++$i) {
			$token = $tokens[$i];

			if (isset($token[1]) === false) {
				continue;
			}

			$class = '';

			switch ($token[0]) {
				case T_NAMESPACE:
					$namespace = '';
					// If there is a namespace, extract it
					while (isset($tokens[++$i][1])) {
						if (in_array($tokens[$i][0], [T_STRING, T_NS_SEPARATOR], true) === true) {
							$namespace .= $tokens[$i][1];
						}
					}
					$namespace .= '\\';
					break;
				case T_CLASS:
				case T_INTERFACE:
				case T_TRAIT:
					// Skip usage of ::class constant
					$isClassConstant = false;
					for ($j = $i - 1; $j > 0; --$j) {
						if (!isset($tokens[$j][1])) {
							break;
						}

						if (T_DOUBLE_COLON === $tokens[$j][0]) {
							$isClassConstant = true;
							break;
						}
						if (in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT], true) === false) {
							break;
						}
					}

					if ($isClassConstant) {
						break;
					}

					// Find the classname
					while (isset($tokens[++$i][1])) {
						$t = $tokens[$i];
						if (T_STRING === $t[0]) {
							$class .= $t[1];
						} elseif ('' !== $class && T_WHITESPACE === $t[0]) {
							break;
						}
					}

					$classes[] = ltrim($namespace . $class, '\\');
					break;
				default:
					break;
			}
		}

		return $classes;
	}

}
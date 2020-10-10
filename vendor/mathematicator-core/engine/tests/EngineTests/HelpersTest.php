<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests;


use Error;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Helpers;
use Nette\Utils\ArrayHash;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class HelpersTest extends TestCase
{
	public function testCreateInstance(): void
	{
		Assert::exception(function () {
			new Helpers;
		}, Error::class);
	}


	public function testGetCurrentUrl(): void
	{
		unset($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST']);

		// CLI mode
		Assert::same(null, Helpers::getCurrentUrl());

		$_SERVER['REQUEST_URI'] = '/kontakt';
		$_SERVER['HTTP_HOST'] = 'baraja.cz';
		$_SERVER['HTTPS'] = 'on';

		// Request mode
		Assert::same('https://baraja.cz/kontakt', Helpers::getCurrentUrl());
	}


	public function testGetBaseUrl(): void
	{
		$_SERVER['REQUEST_URI'] = '/kontakt';
		$_SERVER['HTTP_HOST'] = 'baraja.cz';
		$_SERVER['HTTPS'] = 'on';

		// First call
		Assert::same('https://baraja.cz', Helpers::getBaseUrl(false));

		// Second call is using cache but cache is disabled
		Assert::same('https://baraja.cz', Helpers::getBaseUrl(false));

		// Second call is using cache
		Assert::same('https://baraja.cz', Helpers::getBaseUrl(true));
	}


	public function testGetBaseUrlLocalhost(): void
	{
		$_SERVER['REQUEST_URI'] = '/baraja/www/kontakt';
		$_SERVER['HTTP_HOST'] = 'localhost';
		unset($_SERVER['HTTPS']);

		Assert::same('http://localhost/baraja/www', Helpers::getBaseUrl(false));
	}


	/**
	 * @dataprovider getStrictScalarTypes
	 * @param mixed $expected
	 * @param mixed $haystack
	 */
	public function testStrictScalarType($expected, $haystack, bool $rewriteObjectsToString): void
	{
		Assert::equal($expected, Helpers::strictScalarType($haystack, $rewriteObjectsToString));
	}


	public function testStrictScalarTypeClosure(): void
	{
		$closure = function (): bool {
			return true;
		};

		Assert::same($closure, Helpers::strictScalarType($closure, true));
		Assert::same($closure, Helpers::strictScalarType($closure, false));
	}


	public function testStrictScalarTypeQuery(): void
	{
		$query = new Query('1+2', '1+2');

		Assert::same('1+2', Helpers::strictScalarType($query, true));
		Assert::same('Mathematicator\Engine\Entity\Query', Helpers::strictScalarType($query, false));
	}


	/**
	 * @return mixed[][]
	 */
	public function getStrictScalarTypes(): array
	{
		return [
			[1, 1, false],
			[1, 1, true],
			[true, true, false],
			[[], [], true],
			['Baraja', 'Baraja', false],
			['Nette\Utils\ArrayHash', ArrayHash::from(['key' => 'value']), true],
			['Nette\Utils\ArrayHash', ArrayHash::from(['key' => 'value']), false],
		];
	}
}

(new HelpersTest)->run();

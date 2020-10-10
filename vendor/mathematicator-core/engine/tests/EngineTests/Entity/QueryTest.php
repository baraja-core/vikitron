<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Entity;


use DateTime;
use Mathematicator\Engine\Entity\Query;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class QueryTest extends TestCase
{
	public function testQuery(): void
	{
		$query = new Query('1+2', '1+1');

		Assert::same('1+1', $query->getQuery());
		Assert::same('1+2', $query->getOriginal());
		Assert::same('1+1', (string) $query);
		Assert::same('cs', $query->getLocale());
		Assert::same(8, $query->getDecimals());
		Assert::same(true, $query->isDefaultDecimals());
		Assert::same(50.0755381, $query->getLatitude());
		Assert::same(14.4378005, $query->getLongitude());
		Assert::true($query->getDateTime() instanceof DateTime);
	}


	public function testCustomDecimals(): void
	{
		$query = new Query('PI to 300 digits', 'PI to 300 digits');
		Assert::same(300, $query->getDecimals());
		Assert::same('PI', $query->getQuery());
	}


	public function testFilterTags(): void
	{
		$query = new Query('Dělitelé čísla 360', 'Dělitelé čísla 360'); // Divisors of 360

		Assert::same(['divisors'], $query->getFilteredTags());
		Assert::same('360', $query->getQuery());


		$query = new Query('Prvočíselný rozklad čísla 1024', 'Prvočíselný rozklad čísla 1024'); // Prime decomposition of the number 360
		Assert::same(['prime-factorization'], $query->getFilteredTags());
		Assert::same('1024', $query->getQuery());
	}
}

(new QueryTest)->run();

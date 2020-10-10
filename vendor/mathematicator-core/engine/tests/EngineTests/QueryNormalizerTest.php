<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests;


use Mathematicator\Engine\QueryNormalizer;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../Bootstrap.php';

class QueryNormalizerTest extends TestCase
{

	/** @var QueryNormalizer */
	private $queryNormalizer;


	public function __construct(Container $container)
	{
		$this->queryNormalizer = $container->getByType(QueryNormalizer::class);
	}


	/**
	 * @dataprovider getQueries
	 */
	public function testQueryNormalizer(string $expected, string $query): void
	{
		Assert::same($expected, $this->queryNormalizer->normalize($query));
	}


	/**
	 * @return string[]
	 */
	public function getQueries(): array
	{
		return [
			['5+3', '5 + 3'],
			['123456789', '123456789'],
			['1.05*12000', '1.05 * 12,000'],
			['(5+9)*5', '(5 + 9) * 5'],
			['5+3*(2-1)', '5 + 3 * (2-1'],
			['(10.2+0.5*(2-0.4))*2+(2.1*4)', '(10.2+0.5 * (2-0.4)) * 2 + (2.1 * 4)'],
			['15+3*6', '15 + 3 * 6'],
			['15+3*6', '15 + 3 *  6'],
			['9223372036854775.808000', '9,223,3720,36854,775.808000'],
			['3.14+2.36+91.24+11', '3,14+2,36+91,24+11'],
			['(x^3)/(x^2-1)', '(x^3)/(x^2-1)'],
			['(x-2)^2', '(x-2)^2'],
			['x^3-5*x^2+7*x', 'x^3-5x^2+7x'],
			['((5*x+1)/(2*x-4))-5', '((5x+1)/(2x-4))-5'],
			['(3*x*5-3*x^3)*(x^3-2+sin(x))/(cos(x)*x-sin(x)*x^3)', '(3x*5-3x^3)*(x^3-2+sin(x))/(cos(x)*x-sin(x)*x^3)'],
			['(x^2-4*x+4)/(4*x^3-2*x^4)', '(x^2-4 x+4)/(4 x^3-2 x^4)'],
			['3*(5+1)', '3*(5+1'], // missing right bracket
			['(5+1)+2', '5+1)+2'], // missing left bracket
			['1+2', '(((1+2)))'], // redundant brackets
			['sqrt(3^2+4^2)', 'sqrt (3^2 + 4^2)'],
			['5*x-3 zebry', '5x - 3 zebry'],
			['5*x-3zebry', '5x - 3zebry'],
			['22/6', '\frac{22}{6}'],
			['22/(2/3)', '\frac{22}{\frac{2}{3}}'],
			['INF', 'nekonečno'],
			['INF^2', 'nekonečno^2'],
			['INF+INF', 'nekonečno+inf'],
			['INF-0', '∞-0'],
			['INF-INF', 'INF-inf'],
			['2^4', '2˘4'],
			['16/72 na 8 míst', '16/72 na 8 míst'],
			['PI', '\pi'],
			['PI', '\Pi'],
			['PI', 'π'],
			['6', 'šest'],
			['5+3*2', 'pět plus tři krát dva'],
			['5+3=8', '5 + 3 = 8'],
			['0=1', '0=1'],
			['0=1', '\'0=1'],
			['lineární algebra', 'lineární    algebra'],
			['Analytická geometrie NEW E', 'Analytická geometrie NEW E\''],
			['Vektory', 'Vektory😉'],
			['', '🍁🍃🍂🌰🍁🌿🌾🌼🌻'],
			['nová 1/2', 'novÃ½'],
			['k/6', 'k/6'],
		];
	}
}

(new QueryNormalizerTest(Bootstrap::boot()))->run();

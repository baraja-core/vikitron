<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Test;


use Mathematicator\Engine\QueryNormalizer;
use Model\Math\NumberRewriter;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../../autoload.php';

class QueryNormalizerTest extends TestCase
{

	/**
	 * @var QueryNormalizer
	 */
	private $queryNormalizer;

	/**
	 * @var NumberRewriter
	 */
	private $numberRewriter;

	public function __construct()
	{
		$this->numberRewriter = new NumberRewriter;
		$this->queryNormalizer = new QueryNormalizer($this->numberRewriter);
	}

	/**
	 * @dataprovider getQueries
	 * @param string $expected
	 * @param string $query
	 */
	public function testQueryNormalizer(string $expected, string $query): void
	{
		Assert::same($expected, $this->queryNormalizer->normalize($query));
	}

	/**
	 * @dataprovider getNumberRewriterToNumber
	 * @param string $expected
	 * @param string $query
	 */
	public function testNumberRewriterToNumber(string $expected, string $query): void
	{
		Assert::same($expected, $this->numberRewriter->toNumber($query));
	}

	/**
	 * @return string[]
	 */
	public function getQueries(): array
	{
		return [
			['5+3', '5 + 3'],
			['123456789', '123456789'],
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
			['INF', 'nekonečno'],
			['INF^2', 'nekonečno^2'],
			['INF+INF', 'nekonečno+inf'],
			['INF-0', '∞-0'],
			['INF-INF', 'INF-inf'],
			['2^4', '2˘4'],
			['PI', 'π'],
			['6', 'šest'],
			['5+3*2', 'pět plus tři krát dva'],
		];
	}

	/**
	 * @return string[]
	 */
	public function getNumberRewriterToNumber(): array
	{
		return [
			['5', 'pět'],
			['2 a 3', 'dva a tři'],
		];
	}

}

(new QueryNormalizerTest)->run();
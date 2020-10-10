<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Tests\Formatter;


use Mathematicator\Engine\Formatter\NaturalTextFormatter;
use Mathematicator\Engine\Tests\Bootstrap;
use Nette\DI\Container;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../Bootstrap.php';

class NaturalTextFormatterTest extends TestCase
{

	/** @var NaturalTextFormatter */
	private $naturalTextFormatter;


	public function __construct(Container $container)
	{
		$this->naturalTextFormatter = $container->getByType(NaturalTextFormatter::class);
	}


	/**
	 * @dataprovider getFormatNaturalTextTestCases
	 * @param string $expected
	 * @param string $input
	 */
	public function testFormatNaturalText(string $expected, string $input): void
	{
		Assert::same($expected, $this->naturalTextFormatter->formatNaturalText($input));
	}


	/**
	 * @return string[]
	 */
	public function getFormatNaturalTextTestCases(): array
	{
		return [
			['This is common text.', 'This is common text.'],
			['<div class="latex"><p>\(1+1\)</p><code>1+1</code></div>', '1+1'],
			['Text about 1+1', 'Text about 1+1'],
			['<div class="latex"><p>\(sin\)</p><code>sin</code></div>', 'sin'],
			['<div class="latex"><p>\(\frac{9}{2}\)</p><code>9/2</code></div>', '9/2' . "\n\t\t\t"],
			// ['<div class="latex"><p>\(\frac{k}{2}\)</p><code>k/2</code></div>', 'k/2'] // todo: fix in tokenizer
		];
	}
}

(new NaturalTextFormatterTest(Bootstrap::boot()))->run();

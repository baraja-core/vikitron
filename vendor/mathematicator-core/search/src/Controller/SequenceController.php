<?php

declare(strict_types=1);

namespace Mathematicator\Search\Controller;


use Mathematicator\Calculator\Operation\AddNumbers;
use Mathematicator\Engine\Controller\BaseController;
use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Source;
use Mathematicator\Engine\Step\Step;
use Mathematicator\Statistics\Entity\Sequence;
use Mathematicator\Statistics\StatisticsManager;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Utils\Strings;

final class SequenceController extends BaseController
{

	/** @var StatisticsManager */
	private $statisticManager;

	/** @var Tokenizer */
	private $tokenizer;

	/** @var AddNumbers */
	private $addNumbers;


	/**
	 * @param StatisticsManager $statisticManager
	 * @param Tokenizer $tokenizer
	 * @param AddNumbers $addNumbers
	 */
	public function __construct(StatisticsManager $statisticManager, Tokenizer $tokenizer, AddNumbers $addNumbers)
	{
		$this->statisticManager = $statisticManager;
		$this->tokenizer = $tokenizer;
		$this->addNumbers = $addNumbers;
	}


	public function actionDefault(): void
	{
		$objects = $this->tokenizer->tokensToObject(
			$this->tokenizer->tokenize($this->getQuery())
		);

		$numberLatex = '';
		$numbers = [];
		$numberTokens = [];
		$integers = [];
		$allIntegers = true;

		foreach ($objects as $object) {
			if ($object instanceof NumberToken) {
				$numberTokens[] = $object;
				$numbers[] = $object->getNumber();
				$numberLatex .= ($numberLatex ? ';\ ' : '') . $object->getNumber();

				if ($object->getNumber()->isInteger()) {
					$integers[] = (string) $object->getNumber()->toBigInteger();
				} else {
					$allIntegers = false;
				}
			}
		}

		$this->setInterpret(Box::TYPE_LATEX)
			->setText('\{ ' . $numberLatex . ' \}');

		$this->sum($numberTokens);

		if ($allIntegers === true) {
			$this->integers($integers);
		}
	}


	/**
	 * @param IToken[] $numberTokens
	 */
	private function sum(array $numberTokens): void
	{
		$sum = null;
		$sumSteps = [];
		$numberLatexSum = '';

		foreach ($numberTokens as $numberToken) {
			/** @var NumberToken $numberToken */
			$numberLatexSum .= ($numberLatexSum ? ' + ' : '') . $numberToken->getNumber();
		}

		foreach ($numberTokens as $numberToken) {
			/** @var NumberToken $numberToken */
			if ($sum === null) {
				$sum = $numberToken;
			} else {
				$calculate = $this->addNumbers->process($sum, $numberToken, $this->getQueryEntity());

				$step = new Step(null, null);
				$step->setLatex((string) $sum);
				$step->setTitle($calculate->getTitle());
				$step->setDescription($calculate->getDescription());

				$sumSteps[] = $step;
				$sum = $calculate->getNumber();
			}
		}

		if ($sum !== null) {
			$step = new Step(null, null);
			$step->setLatex((string) $sum->getNumber());
			$step->setTitle('Součet řady');

			$sumSteps[] = $step;
		}

		if ($sum !== null) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Součet')
				->setText(
					'\\sum\ \{ '
					. (Strings::length($numberLatexSum) < 64
						? $numberLatexSum
						: '\ \\dotsb\ '
					)
					. '\}\ =\ '
					. $sum->getNumber()
				)
				->setSteps($sumSteps);
		}
	}


	/**
	 * @param string[] $integers
	 */
	private function integers(array $integers): void
	{
		$sequencesBuffer = '';
		$sequenceAuthors = [];
		$integersString = implode(', ', $integers);

		foreach ($this->statisticManager->getSequences($integers) as $sequence) {
			$formula = $sequence->getFormula();
			$example = $sequence->getDataType('e');
			$sequenceAuthors[] = (string) $sequence->getDataType('A');

			if ($sequencesBuffer) {
				$sequencesBuffer .= '<hr>';
			}

			$sequencesBuffer .=
				'<a href="' . $this->linkToSearch($sequence->getAId()) . '">'
				. $sequence->getAId()
				. '</a>&nbsp;' . preg_replace(
					'/^' . $integersString . '/',
					'<span style="background:rgba(253,245,206,1);border-radius:2px;padding:0 4px">'
					. $integersString
					. '</span>',
					implode(', ', $sequence->getSequence())
				) . ', ...';

			if ($formula) {
				$sequencesBuffer .= '<div class="text-center p-2 mt-2" style="background:#eee">';

				$formulaIterator = 0;
				foreach (explode("\n", htmlspecialchars($formula)) as $formulaItem) {
					$sequencesBuffer .= ($formulaIterator > 0 ? '<hr>' : '') . $formulaItem;
					if (++$formulaIterator >= 3) {
						break;
					}
				}

				$sequencesBuffer .= '</div>';
			}

			if ($example && Strings::contains($example, '   ')) {
				$sequencesBuffer .= '<div class="p-2 mt-2" style="border:1px solid #aaa">'
					. $this->formatBr($sequence, $example)
					. '</div>';
			}
		}

		if ($sequencesBuffer) {
			$this->addBox(Box::TYPE_HTML)
				->setTitle('Pokračování posloupnosti')
				->setText($sequencesBuffer);

			$source = new Source(
				'OEIS',
				'https://oeis.org',
				'On-line Encyklopedie celočíselných posloupností.'
			);
			$source->setAuthors($sequenceAuthors);

			$this->addSource($source);
		}
	}


	/**
	 * @param Sequence $sequence
	 * @param string $data
	 * @param int $limit
	 * @return string
	 */
	private function formatBr(Sequence $sequence, string $data, int $limit = 10): string
	{
		$return = '';
		$lastPre = false;
		$lines = 1;

		foreach (explode("\n", $this->formatLinks(htmlspecialchars($data))) as $line) {
			if (Strings::contains($line, '   ')) {
				$return .= ($lastPre ? '' : '<pre class="p-2 my-2" style="border:1px solid #aaa">') . $line . "\n";
				$lastPre = true;
			} else {
				$return .= ($lastPre ? '</pre>' : '') . '<p>' . $line . '</p>';
				$lastPre = false;

				if ($lines >= $limit) {
					$return .= '<p><a href="' . $this->linkToSearch($sequence->getAId()) . '">'
						. 'Pokračování na samostatné stránce'
						. '</a></p>';
					break;
				}
			}

			$lines++;
		}

		return $return;
	}


	/**
	 * @param string $data
	 * @return string
	 */
	private function formatLinks(string $data): string
	{
		return (string) preg_replace_callback('/\s(A\d{6})\s/', function (array $row): string {
			return ' <a href="' . $this->linkToSearch($row[1]) . '">' . $row[1] . '</a> ';
		}, $data);
	}
}

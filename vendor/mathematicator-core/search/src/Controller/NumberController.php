<?php

declare(strict_types=1);

namespace Mathematicator\Search\Controller;


use Brick\Math\RoundingMode;
use Mathematicator\Calculator\Numbers\NumberHelper;
use Mathematicator\Calculator\Step\Model\RomanIntSteps;
use Mathematicator\Engine\Controller\BaseController;
use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Exception\DivisionByZeroException;
use Mathematicator\Engine\Helper\Czech;
use Mathematicator\Engine\Helper\DateTime;
use Mathematicator\Engine\Step\Step;
use Mathematicator\Numbers\Latex\MathLatexToolkit;
use Mathematicator\Numbers\SmartNumber;
use Nette\Utils\Strings;

final class NumberController extends BaseController
{

	/**
	 * @var NumberHelper
	 * @inject
	 */
	public $numberHelper;

	/**
	 * @var RomanIntSteps
	 * @inject
	 */
	public $romanToIntSteps;

	/** @var SmartNumber */
	private $number;


	public function actionDefault(): void
	{
		$number = $this->getQuery();
		$isRoman = false;
		if ($this->numberHelper->isRoman($this->getQuery())) {
			$number = NumberHelper::romanToInt($this->getQuery());
			$isRoman = true;

			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Převod do Arabských číslic')
				->setText($number)
				->setSteps($this->romanToIntSteps->getRomanToIntSteps($this->getQuery()));
		}
		if (\in_array(strtolower(Strings::toAscii((string) $number)), ['pi', 'ludolfovo cislo'], true) === true) {
			$this->aboutPi();

			return;
		}
		if ($number > 10e10) {
			$this->addBox(Box::TYPE_TEXT)
				->setTitle($this->translator->translate('search.information'))
				->setText('Podporu pro vysoká čísla připravujeme.');

			return;
		}
		try {
			$this->number = SmartNumber::of($number);
			$this->actionNumericalField($this->number);
		} catch (DivisionByZeroException $e) {
			$this->actionDivisionByZero((string) $number);

			return;
		}
		if ($this->number->isInteger()) {
			$this->setInterpret(
				Box::TYPE_LATEX,
				$isRoman
					? '\\text{' . Strings::upper($this->getQuery()) . '} = ' . $this->number->toLatex()
					: (string) $this->number->toLatex()
			);

			$this->actionInteger();
		} elseif (!$this->number->isInteger()) {
			$fraction = $this->number->toBigRational();

			$this->setInterpret(
				Box::TYPE_LATEX,
				MathLatexToolkit::frac((string) $fraction->getNumerator(), (string) $fraction->getDenominator())
				. ' ≈ '
				. number_format($this->number->toFloat(), $this->getQueryEntity()->getDecimals(), '.', ' ')
			);

			$this->actionFloat();
		}
	}


	private function actionInteger(): void
	{
		$int = $this->number->toBigInteger();

		if ($int->isGreaterThanOrEqualTo(1750) && $int->isLessThanOrEqualTo(2300)) {
			$this->actionYear((int) date('Y'), $int->toInt());
		}
		if ($int->isEqualTo(42)) {
			// Easter egg
			$this->addBox(Box::TYPE_TEXT)
				->setTitle('Ahoj, stopaři!')
				->setText('Odpověď na Základní otázku života, Vesmíru a tak vůbec');
		}
		if ($int->isLessThanOrEqualTo(1000000)) {
			$this->numberSystem((string) $int);
			$this->alternativeRewrite();
		} else {
			if ($int->isLessThan('2^64')) {
				$this->timestamp((string) $int);
			}

			$this->bigNumber((string) $int);
		}
		if ($int->isGreaterThan(0)) {
			$this->primeFactorization();
		}
		if ($int->isGreaterThan(0) && $int->isLessThanOrEqualTo(1000000)) {
			$this->divisors();
		}
		if ($int->isGreaterThan(0) && $int->isLessThanOrEqualTo(50)) {
			$this->graphicInt();
		}
	}


	private function actionDivisionByZero(string $number): void
	{
		preg_match('/^(?<top>.+?)\/(?<bottom>.+)$/', $number, $match);

		$this->setInterpret(
			Box::TYPE_LATEX,
			'\frac{' . $match['top'] . '}{' . $match['bottom'] . '}\ \simeq\ ???'
		);

		$step = new Step(
			$this->translator->translate('search.divisionByZero'),
			null,
			$this->translator->translate('search.divisionByZeroDesc', [
				'number' => (int) $match['top'],
			])
		);

		$this->addBox(Box::TYPE_TEXT)
			->setTitle($this->translator->translate('search.solution'))
			->setText('Tento příklad nelze v reálných číslech vyřešit z důvodu dělení nulou.')
			->setSteps([$step]);
	}


	private function actionFloat(): void
	{
		if (!$this->number->toBigRational()->getDenominator()->isEqualTo(1)) {
			$this->convertToFraction();
		}
	}


	private function actionNumericalField(SmartNumber $number): void
	{
		$steps = [];
		$step = new Step('Číselné obory', null, 'Určíme číselný obor podle tabulky.');
		$steps[] = $step;

		$step = new Step('Přehled číselných oborů', null);

		$numberTypesHtml = '';
		$numberTypes = [
			'N' => 'Přirozená čísla: \(1, 2, 3, 100, 105, 1006\), ...',
			'Z' => 'Celá čísla: \(-10, -1, 0, 1, 2, 3\), ...',
			'Q' => 'Racionální čísla: \(-10, -1, 0, \frac{1}{3}, \frac{5}{2}, 2.5, 3\), ...',
			'R' => 'Reálná čísla: \(-10, -1, 0, 1/3, \frac{5}{2}, 2.5, \pi, e, \sqrt{2}, 3\), ...',
			'R \setminus Q' => 'Iracionální čísla: \(\pi, e, \sqrt{2}\), ...',
			'C' => 'Komplexní čísla: \(-10, -1, 0, \frac{1}{3}, \frac{5}{2}, 2,5, \pi, e, \sqrt{2}, 3, 1+2i, 3-10i, 2i\), ...',
		];

		foreach ($numberTypes as $type => $description) {
			$numberTypesHtml .= '<tr>'
				. '<th class="text-center">\(\mathbb{' . $type . '}\)</th>'
				. '<td>' . $description . '</td>'
				. '</tr>';
		}

		$step->setDescription('<table>' . $numberTypesHtml . '</table>', true);

		$steps[] = $step;

		$step = new Step(null, null);
		$stepDescription = [];

		if ($number->isInteger()) {
			$text = 'Celé reálné číslo';
			$stepDescription[] = 'Je celé číslo.';

			if ($number->toBigInteger()->isGreaterThan(0)) {
				$text = 'Přirozené celé reálné číslo';
				$stepDescription[] = 'Je větší než nula.';
			} elseif ($number->toBigInteger()->isLessThan(0)) {
				$stepDescription[] = 'Je záporné číslo.';
			} else {
				$stepDescription[] = 'Je nula.';
			}
		} else {
			$text = 'Reálné číslo';
			$stepDescription[] = 'Není celé číslo.';

			if ($number->isEqualTo($number->toBigDecimal()->toScale(3, RoundingMode::HALF_UP))) {
				$text = 'Racionální reálné číslo (vyjádřitelné zlomkem)';
				$stepDescription[] = 'Má konečně dlouhý desetinný rozvoj (počet cifer za desetinnou čárkou), proto lze vyjádřit zlomkem.';
			}
		}

		$step->setTitle('Splněné předpoklady');
		$step->setDescription('<ul><li>' . implode('</li><li>', $stepDescription) . '</li></ul>', true);
		$steps[] = $step;

		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Číselný obor')
			->setText($text . ($number->toBigInteger()->isNegative() ? ' (záporné číslo)' : ''))
			->setSteps($steps);
	}


	private function actionYear(int $currentYear, int $year): void
	{
		$diff = abs($currentYear - $year);
		$stepDescription = null;

		if ($diff === 0) {
			$text = 'Rok ' . $year . ' je právě teď.';
			$stepText = null;
			$stepDescription = 'Rok ' . $year . ' je podle kalendáře aktuální.';
		} elseif ($currentYear < $year) {
			$text = 'Za ' . Czech::inflection($diff, ['rok', 'roky', 'let']);
			$stepText = $year . ' - ' . $currentYear . ' = ' . $diff;
			$stepDescription = 'Od požadovaného roku odečteme aktuální rok.';
		} else {
			$text = 'Před ' . Czech::inflection($diff, ['rokem', 'lety', 'lety']);
			$stepText = $currentYear . '-' . $year . ' = ' . $diff;
			$stepDescription = 'Od aktuálního roku odečteme požadovaný rok.';
		}

		$step = new Step(
			$this->translator->translate('search.solution'),
			$stepText,
			$stepDescription
		);

		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Čas od dnes')
			->setText($text)
			->setSteps([$step]);
	}


	private function numberSystem(string $int): void
	{
		if ($int < 0) {
			return;
		}

		$bin[] = Strings::upper(decbin((int) $int)) . '_{2}';
		$bin[] = Strings::upper(decoct((int) $int)) . '_{8}';
		$bin[] = Strings::upper($int) . '_{10}';
		$bin[] = '\\text{' . Strings::upper(dechex((int) $int)) . '}_{16}';

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Převod číselných soustav')
			->setText(implode("\n", $bin));
	}


	private function alternativeRewrite(): void
	{
		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Převod do Římských číslic')
			->setText(NumberHelper::intToRoman((string) $this->number->toBigInteger()))
			->setSteps($this->romanToIntSteps->getIntToRomanSteps($this->number->toBigInteger()));
	}


	private function timestamp(string $int): void
	{
		$currentTimestamp = \time();
		$dateDiff = abs($currentTimestamp - (int) $int);

		$timestamp = '<p><b>' . DateTime::getDateTimeIso((int) $int) . '</b></p>'
			. '<p>'
			. ($currentTimestamp < $int
				? 'Bude za ' . $dateDiff . ' sekund (' . DateTime::formatTimeAgo($currentTimestamp - $dateDiff) . ')'
				: 'Bylo před ' . $dateDiff . ' sekundami (' . DateTime::formatTimeAgo((int) $int) . ').'
			)
			. '</p>'
			. ((int) date('Y', (int) $int) >= 2038
				? '<p class="text-secondary">Pozor: Po roce 2038 nemusí tento timestamp fungovat na 32-bitových počítačích, protože překračuje maximální hodnotu, kterou je možné uložit do 32-bitového integeru.</p>'
				: '');

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Unix Timestamp | Čas serveru: ' . date('d. m. Y H:i:s'))
			->setText($timestamp);
	}


	private function primeFactorization(): void
	{
		$int = $this->number->toBigInteger();
		$factors = $this->numberHelper->pfactor((string) $int);

		if (\count($factors) === 1) {
			$this->addBox(Box::TYPE_TEXT)
				->setTitle('Prvočíselný rozklad')
				->setText('Číslo ' . $int . ' je prvočíslo, proto nelze dále rozložit.')
				->setTag('prime-factorizationx');
		} else {
			$outputFactor = '';
			$items = 0;
			$primaries = 0;

			foreach (\array_count_values($factors) as $b => $e) {
				if ($outputFactor) {
					$outputFactor .= ' \cdot ';
				}
				$items += $e;
				$primaries++;
				if (preg_match('/^(.+)E[+-]?(.+)$/', (string) $b, $bParser)) {
					$outputFactor .= '\left({' . $bParser[1] . '}^{' . $bParser[2] . '}\right)';
				} else {
					$outputFactor .= $b . ($e > 1 ? '^{' . $e . '}' : '');
				}
			}

			$this->addBox(Box::TYPE_LATEX)
				->setTitle(
					'Prvočíselný rozklad'
					. ' | ' . Czech::inflection($items, ['člen', 'členy', 'členů'])
					. ' | ' . Czech::inflection($primaries, ['prvočíslo', 'prvočísla', 'prvočísel'])
				)
				->setText($outputFactor)
				->setTag('prime-factorization');
		}
	}


	private function divisors(): void
	{
		$int = $this->number->toBigInteger();
		$divisors = $this->sort($this->numberHelper->getDivisors((string) $int));
		$title = 'Dělitelé čísla ' . $int
			. ' | ' . Czech::inflection(\count($divisors), ['dělitel', 'dělitelé', 'dělitelů'])
			. ' | Součet: ' . array_sum($divisors);

		if (\count($divisors) < 5) {
			$divisor = ['!Dělitel'];
			$share = ['!Podíl'];

			for ($i = 0; isset($divisors[$i]); $i++) {
				$divisor[] = '=' . $divisors[$i] . '=';
				$share[] = '=' . $int->dividedBy($divisors[$i]) . '=';
			}

			$box = $this->addBox(Box::TYPE_TABLE)->setTable([$divisor, $share]);
		} else {
			$box = $this->addBox(Box::TYPE_HTML)->setText(implode(', ', $divisors));
		}

		$box->setTitle($title)->setTag('divisors');

		// TODO: 'hiddenContent' => 'Vlastnosti dělitelnosti'
	}


	/**
	 * @param int[]|string[] $array
	 * @return int[]|string[]
	 */
	private function sort(array $array): array
	{
		$return = [];
		foreach ($array as $item) {
			$return[] = (string) $item;
		}

		sort($return);

		return $return;
	}


	private function graphicInt(): void
	{
		$int = $this->number->toBigInteger();
		$render = '';
		for ($i = 1; $int->isGreaterThanOrEqualTo($i); $i++) {
			$render .= '<div style="float: left; width: 8px; height: 8px; background: #EA4437; margin: 3px;"></div>';
		}

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Grafická reprezentace')
			->setText('<div style="overflow: auto;">' . $render . '</div>');
	}


	private function aboutPi(): void
	{
		$this->setInterpret(Box::TYPE_LATEX, MathLatexToolkit::PI);

		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Přibližná hodnota π | Ludolfovo číslo | Přesnost: ' . $this->getQueryEntity()->getDecimals())
			->setText('π ≈ ' . $this->numberHelper->getPi($this->getQueryEntity()->getDecimals()) . ' …');
	}


	private function convertToFraction(): void
	{
		$factor = $this->number->toBigRational();

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Zlomkový zápis | Nejlepší odhad')
			->setText(
				MathLatexToolkit::frac((string) $factor->getNumerator(), (string) $factor->getDenominator())
				. ' ≈ '
				. number_format($factor->toFloat(), $this->getQueryEntity()->getDecimals(), '.', ' ')
			);

		if ($factor->getNumerator()->isGreaterThan($factor->getDenominator())) {
			$int = $factor->getNumerator()->dividedBy($factor->getDenominator(), RoundingMode::DOWN);
			$fraction = $factor->getNumerator()->minus($int)->multipliedBy($factor->getDenominator());

			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Složený zlomek')
				->setText($int . '\ ' . MathLatexToolkit::frac((string) $fraction->toBigInteger(), (string) $factor->getDenominator()));
		}
	}


	private function bigNumber(string $int): void
	{
		$countNumbers = \strlen($int);
		$uniqueNumbers = \count(\array_unique(str_split($int)));

		if ($uniqueNumbers <= 4 && $uniqueNumbers >= 2) {
			for ($i = $countNumbers - 1; $i >= 4; $i--) {
				if ($countNumbers % $i === 0 && $countNumbers / $i >= 4) {
					$this->addBox(Box::TYPE_HTML)
						->setTitle('Vizualizace | Rozměr: ' . $i . ' x ' . ($countNumbers / $i))
						->setText(
							$this->renderTable($int, $countNumbers / $i, $i)
						);
				}
			}
		}
	}


	private function renderTable(string $data, int $x, int $y): string
	{
		$return = '';
		$iterator = 0;
		$colors = ['green', 'black', 'red', 'blue'];
		$colorCache = [];

		for ($i = 0; $i < $x; $i++) {
			for ($j = 0; $j < $y; $j++) {
				$char = $data[$iterator];
				$return .= '<span style="color:' . ($colorCache[$char] ?? $colors[\count($colorCache)]) . '">'
					. htmlspecialchars($char, ENT_QUOTES)
					. '</span>';
				$iterator++;
			}
			$return .= '<br>';
		}

		return $return;
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use App\VikiTron\Model\Number\NumberHelper;
use Mathematicator\Calculator\Calculator;
use Mathematicator\Calculator\Step;
use Mathematicator\Engine\DivisionByZero;
use Mathematicator\Engine\Helper\Czech;
use Mathematicator\Engine\MathErrorException;
use Mathematicator\Engine\Translator;
use Mathematicator\Engine\UndefinedOperationException;
use Mathematicator\Search\Box;
use Mathematicator\Tokenizer\Token\ComparatorToken;
use Mathematicator\Tokenizer\Token\EquationToken;
use Mathematicator\Tokenizer\Token\InfinityToken;
use Mathematicator\Tokenizer\Token\IToken;
use Mathematicator\Tokenizer\Token\NumberToken;
use Mathematicator\Tokenizer\Token\OperatorToken;
use Mathematicator\Tokenizer\Tokenizer;
use Model\Math\MathFunction\FunctionDoesNotExistsException;
use Model\Math\Step\StepFactory;
use Nette\Application\LinkGenerator;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

class NumberCounterController extends BaseController
{

	/**
	 * @var Translator
	 */
	private $translator;

	/**
	 * @var Tokenizer
	 */
	private $tokenizer;

	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @var Calculator
	 */
	private $calculator;

	/**
	 * @var Number
	 */
	private $number;

	/**
	 * @var string[]
	 */
	private $functions;

	/**
	 * @var bool
	 */
	private $haveResult = false;

	/**
	 * @param \string[] $functions
	 * @param LinkGenerator $linkGenerator
	 * @param Translator $translator
	 * @param Tokenizer $tokenizer
	 * @param StepFactory $stepFactory
	 * @param Calculator $calculator
	 * @param NumberHelper $number
	 */
	public function __construct(
		array $functions,
		LinkGenerator $linkGenerator,
		Translator $translator,
		Tokenizer $tokenizer,
		StepFactory $stepFactory,
		Calculator $calculator,
		NumberHelper $number
	)
	{
		parent::__construct($linkGenerator);
		$this->functions = $functions;
		$this->translator = $translator;
		$this->tokenizer = $tokenizer;
		$this->stepFactory = $stepFactory;
		$this->calculator = $calculator;
		$this->number = $number;
	}

	public function actionDefault(): void
	{
		$tokens = $this->tokenizer->tokenize($this->getQuery());
		$objects = $this->tokenizer->tokensToObject($tokens);

		$this->setInterpret(Box::TYPE_LATEX)
			->setText($this->tokenizer->tokensToLatex($objects));

		$calculator = [];
		$steps = [];

		try {
			$calculatorResult = $this->calculator->calculate($objects);
			$calculator = $calculatorResult->getResultTokens();
			$steps = $calculatorResult->getSteps();
		} catch (DivisionByZero $e) {
			$fraction = $e->getFraction();

			$step = $this->stepFactory->create();
			$step->setTitle('Dělení nulou');
			$step->setDescription($this->translator->getTranslate('divisionByZero', [
				'count' => $fraction[0],
			]));

			$this->addBox(Box::TYPE_TEXT)
				->setTitle('Řešení')
				->setText('Tento příklad nelze v reálných číslech vyřešit z důvodu dělení nulou.')
				->setSteps([$step]);

			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Řešení')
				->setText('\frac{' . $fraction[0] . '}{' . $fraction[1] . '} = \frac{1}{0} \simeq \infty');

			$this->haveResult = true;
		} catch (UndefinedOperationException $e) {
			$this->actionUndefinedSolution();
			$this->haveResult = true;
		} catch (FunctionDoesNotExistsException $e) {
			$supportedFunctions = '';

			foreach ($this->functions as $function) {
				if (preg_match('/^\w+$/', $function)) {
					$supportedFunctions .= ($supportedFunctions ? ', ' : '')
						. '<code>' . $function . '()</code>';
				}
			}

			$this->addBox(Box::TYPE_TEXT)
				->setTitle('Funkce neexistuje')
				->setText('<p>Funkci <b>' . $e->getFunction() . '()</b> neumíme zpracovat.</p>'
					. '<p>Seznam podporovaných funkcí:</p>'
					. $supportedFunctions);

			$this->haveResult = true;
		} catch (MathErrorException $e) {
			$this->addBox(Box::TYPE_TEXT)
				->setTitle('Řešení')
				->setText(
					'Tato úloha nemá řešení, protože provádíte nepovolenou matematickou operaci.'
					. "\n\n" . 'Detaily: ' . $e->getMessage()
				);

			$this->haveResult = true;
		}

		if (\count($calculator) === 1) {
			$this->renderResultToken($calculator[0], $steps);

			if ($this->isSimpleProblem($objects)) {
				$this->actionSimpleProblem($objects);
			} elseif ($this->isAddNumbers($objects)) {
				$this->actionAddNumbers($objects);
			}
		} elseif (isset($calculator[0], $calculator[1], $calculator[2])
			&& ($calculator[1] instanceof EquationToken || $calculator[1] instanceof ComparatorToken)
		) {
			if ($calculator[0] instanceof NumberToken && $calculator[2] instanceof NumberToken) {
				$this->actionBoolean($calculator[0], $calculator[2], $calculator[1], $steps);
			} else {
				$this->addBox(Box::TYPE_LATEX)
					->setTitle('Řešení výroku')
					->setText($calculator[0]->getToken() . ' ' . $calculator[1]->getToken() . ' ' . $calculator[2]->getToken());
			}
			$this->haveResult = true;
		} elseif ($calculator !== [] && $calculator !== $objects) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Upravený zápis')
				->setText($this->tokenizer->tokensToLatex($calculator))
				->setSteps($steps);

			$this->haveResult = true;
		}

		if ($this->haveResult === false) {
			$this->actionError($steps);
		}
	}

	private function actionError(array $steps): void
	{
		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Nepodařilo se nalézt řešení')
			->setText('Tento vstup bohužel neumíme upravit.')
			->setSteps($steps);
	}

	/**
	 * @param IToken[] $tokens
	 */
	private function actionSimpleProblem(array $tokens): void
	{
		$buffer = '';

		foreach ($tokens as $token) {
			$buffer .= '<div style="border:1px solid #aaa;float:left;min-height:70px;margin:4px;padding:4px">';

			if ($token instanceof NumberToken) {
				$int = $token->getNumber()->getInteger();
				if ($int >= 0) {
					$buffer .= $this->renderNumber($int);
				} else {
					$buffer .= '<div style="overflow:auto;margin:-8px">'
						. '<div style="float:left;min-height:70px;margin:4px 4px 4px 12px">'
						. '<span style="font-size:27pt;padding:8px">-</span>'
						. '</div>'
						. '<div style="border-left:1px solid #aaa;float:left;min-height:70px;margin:4px;padding:4px">'
						. $this->renderNumber((string) abs($int))
						. '</div></div>';
				}
			} else {
				$buffer .= '<span style="font-size:27pt;padding:8px">' . $token->getToken() . '</span>';
			}

			$buffer .= '</div>';
		}

		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Grafická reprezentace příkladu')
			->setText('<div style="overflow:auto">' . $buffer . '</div>');

		$this->haveResult = true;
	}

	/**
	 * @param IToken[]|NumberToken[] $tokens
	 */
	private function actionAddNumbers(array $tokens): void
	{
		$this->addBox(Box::TYPE_HTML)
			->setTitle('Sčítání pod sebou')
			->setText(
				$this->number->getAddStepAsHtml(
					$tokens[0]->getNumber()->getInput(),
					$tokens[2]->getNumber()->getInput(),
					true
				)
			);

		$this->haveResult = true;
	}

	private function actionUndefinedSolution(): void
	{
		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Řešení')
			->setText('Nemá žádné řešení, jde o neurčitý výraz. Není definováno.');

		$undefinedForms = [
			'\frac{0}{0}',
			'\frac{\infty}{\infty}',
			'0\ \cdot\ \infty',
			'\infty\ -\ \infty',
			'{1}^{\infty}',
			'{\infty}^{0}',
			'{(-1)}^{\infty}',
			'{0}^{i}',
			'{0}^{0}',
			'{z}^{\infty}\ \text{for}\ \left\|z\right\|\ =\ 1',
			'\sqrt[0]{x}',
		];

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Přehled neurčitých výrazů')
			->setText(implode("\n", $undefinedForms));

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Limita typu 0<sup>0</sup>')
			->setText(implode("\n", [
				'\lim\limits_{x\to{0}^{+}} {x}^{x}\ =\ 1',
				'\lim\limits_{x\to{0}^{+}} {0}^{x}\ =\ 0',
			]));
	}

	/**
	 * @param IToken $tokenA
	 * @param IToken $tokenB
	 * @param ComparatorToken $comparator
	 * @param Step[] $steps
	 * @return bool
	 */
	private function actionBoolean(IToken $tokenA, IToken $tokenB, ComparatorToken $comparator, array $steps): bool
	{
		$numberA = $tokenA->getToken();
		$numberB = $tokenB->getToken();

		$isTrue = function (IToken $a, IToken $b, ComparatorToken $comparator) {
			$numberA = $a->getToken();
			$numberB = $b->getToken();

			if ($comparator instanceof EquationToken || $comparator->getToken() === '=') {
				return $numberA === $numberB;
			}

			switch ($comparator->getToken()) {
				case '<<':
					return $numberA << $numberB;

				case '>>':
					return $numberA >> $numberB;

				case '<=>':
				case '<>':
				case '!==':
				case '!=':
					return $numberA !== $numberB;

				case '<=':
					return $numberA <= $numberB;

				case '>=':
					return $numberA >= $numberB;

				case '<':
					return $numberA < $numberB;

				case '>':
					return $numberA > $numberB;
			}

			return false;
		};

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Řešení výroku')
			->setText($isTrue($tokenA, $tokenB, $comparator)
				? '<b style="color:green">PRAVDA</b>'
				: '<b style="color:red">NEPRAVDA</b>')
			->setSteps($steps);

		if ($numberA === $numberB) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Řešení')
				->setText($numberA);

			return true;
		}

		$overlap = '';

		if (Validators::isNumeric($numberA) && Validators::isNumeric($numberB)) {
			for ($i = 0; isset($numberA[$i], $numberB[$i]); $i++) {
				if ($numberA[$i] === $numberB[$i]) {
					$overlap .= $numberA[$i];
				} else {
					break;
				}
			}
		}

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Porovnání řešení')
			->setText($numberA . "\n" . $numberB);

		if (\strlen($overlap) > 2) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle(
					'Překryv řešení | Přesnost: '
					. Czech::inflection(\strlen($overlap), ['místo', 'místa', 'míst'])
				)
				->setText($overlap);
		}

		$calculatorResult = $this->calculator->calculateString(
			$numberA . '-' . $numberB
		);
		$calculator = $calculatorResult->getResultTokens();
		$steps = $calculatorResult->getSteps();

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Rozdíl řešení')
			->setText($calculator[0]->getToken())
			->setSteps($steps);

		$calculatorShareResult = $this->calculator->calculateString(
			$numberA . '/' . $numberB
		);
		$calculatorShare = $calculatorShareResult->getResultTokens();
		$stepsShare = $calculatorShareResult->getSteps();

		/** @var NumberToken[] $calculatorShare */

		if ($calculatorShare[0] instanceof NumberToken) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Podíl řešení')
				->setText('\frac{' . $numberA . '}{' . $numberB . '}'
					. '\ =\ ' . $calculatorShare[0]->getToken()
					. '\ ≈\ ' . $calculatorShare[0]->getNumber()->getFloatString())
				->setSteps($stepsShare);
		}

		return true;
	}

	/**
	 * @param IToken $token
	 * @param Step[] $steps
	 */
	private function renderResultToken(IToken $token, array $steps = []): void
	{
		if ($token instanceof NumberToken) {
			if ($token->getNumber()->isInteger()) {
				$result = $token->getNumber()->getInteger();
			} else {
				$fraction = $token->getNumber()->getFraction();
				$result = ($fraction[0] < 0 ? '-' : '')
					. '\frac{' . abs($fraction[0]) . '}'
					. '{' . $fraction[1] . '} ≈ '
					. preg_replace('/^(.+)[eE](.+)$/', '$1\ \cdot\ {10}^{$2}', $token->getNumber()->getFloat());
			}

			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Řešení')
				->setText($result)
				->setSteps($steps);

			if ($token->getNumber()->isInteger()) {
				$int = $token->getNumber()->getInteger();
				$numberLength = \strlen($int);
				if ($numberLength > 8) {
					$this->addBox(Box::TYPE_TEXT)
						->setTitle('Délka čísla')
						->setText(Czech::inflection($numberLength, [
							'desetinná cifra',
							'desetinné cifry',
							'desetinných cifer',
						]));

					if (preg_match('/^(\d)((\d{1,7}).*?)$/', $int, $intParser)) {
						$this->addBox(Box::TYPE_LATEX)
							->setTitle('Desetinná aproximace')
							->setText($intParser[1] . '.' . $intParser[3] . '\ \cdot\ {10}^{' . \strlen($intParser[2]) . '}');
					}

					if (Strings::endsWith($int, '0')) {
						$zeros = preg_replace('/^\d+?(0+)$/', '$1', $int);
						$this->addBox(Box::TYPE_LATEX)
							->setTitle('Počet nul na konci')
							->setText((string) \strlen($zeros));
					}
				}
			}

			$this->haveResult = true;
		}

		if ($token instanceof InfinityToken) {
			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Řešení')
				->setText('\infty')
				->setSteps($steps);

			$this->addBox(Box::TYPE_LATEX)
				->setTitle('Číselný obor')
				->setText('\mathbb{R}\ \cup\left\{+\infty,-\infty\right\}' . "\n" . '{\mathbb{R}}^{*}');

			$this->haveResult = true;
		}
	}

	/**
	 * @param IToken[] $tokens
	 * @return bool
	 */
	private function isSimpleProblem(array $tokens): bool
	{
		$tokensCount = \count($tokens);

		if ($tokensCount < 3 || $tokensCount > 12) {
			return false;
		}

		foreach ($tokens as $token) {
			if (!
			(
				(
					$token instanceof OperatorToken
					&& \in_array($token->getToken(), ['+', '-'])
				) || (
					$token instanceof NumberToken
					&& $token->getNumber()->isInteger()
					&& $token->getNumber()->getInteger() <= 20
				)
			)
			) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param IToken[] $tokens
	 * @return bool
	 */
	private function isAddNumbers(array $tokens): bool
	{
		return \count($tokens) === 3
			&& $tokens[0] instanceof NumberToken
			&& $tokens[1] instanceof OperatorToken
			&& $tokens[1]->getToken() === '+'
			&& $tokens[2] instanceof NumberToken;
	}

	/**
	 * @param string $int
	 * @return string
	 */
	private function renderNumber(string $int): string
	{
		$render = '';
		for ($i = 1; $i <= $int; $i++) {
			$render .= '<div style="float: left; width: 8px; height: 8px; background: #EA4437; margin: 3px;"></div>';
		}

		return '<div style="max-width:70px">' . $render . '</div>';
	}

}

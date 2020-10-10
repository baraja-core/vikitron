<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step\Model;


use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Mathematicator\Engine\Step\Step;
use Nette\Utils\Strings;
use function strlen;

final class RomanIntSteps
{

	/** @var int[] */
	private static $romanNumber = [
		'm' => 1000000,
		'd' => 500000,
		'c' => 100000,
		'l' => 50000,
		'x' => 10000,
		'v' => 5000,
		'M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1,
	];

	/** @var int[] */
	private static $translateTable = [
		'I' => 1,
		'V' => 5,
		'X' => 10,
		'L' => 50,
		'C' => 100,
		'D' => 500,
		'M' => 1000,
	];

	/** @var string[] */
	private static $translateTableCzechHelp = [
		'I' => 'Ivan',
		'V' => 'Vedl',
		'X' => 'Xenii',
		'L' => 'Lesem',
		'C' => 'Cestou',
		'D' => 'Do',
		'M' => 'Města',
	];

	/** @var string[] */
	private static $translateTableInverse = [
		1 => 'I',
		5 => 'V',
		10 => 'X',
		50 => 'L',
		100 => 'C',
		500 => 'D',
		1000 => 'M',
	];


	/**
	 * @return Step[]
	 */
	public function getRomanToIntSteps(string $roman): array
	{
		$steps = [];

		$step = new Step();
		$step->setTitle('Převodní tabulka');
		$step->setDescription(
			'Ivan Vedl Xenii Lesem Cestou Do Města.' . $this->getTranslateTable($roman),
			true
		);

		$steps[] = $step;

		$roman = Strings::upper($roman);
		$romanLength = Strings::length($roman);
		$return = 0;
		$lastPosition = 0;
		for ($i = 0; $i < $romanLength; $i++) {
			$step = new Step();
			$x = self::$romanNumber[$roman[$i]];
			if ($i + 1 < strlen($roman) && ($nextToken = self::$romanNumber[$roman[$i + 1]]) > $x) {
				$return += $nextToken - $x;
				$step->setTitle(
					$this->getTitleBasic($lastPosition, $i + 1, $roman)
					. 'Přičteme hodnotu ' . ($nextToken - $x),
					true
				);
				$step->setDescription(
					'Když menší římská číslice předchází větší, tak se menší číslo odečítá:'
					. '<div class="my-3 text-center">'
					. '\(\textrm{' . self::$translateTableInverse[$x] . self::$translateTableInverse[$nextToken] . '}'
					. ' = ' . $nextToken . ' - ' . $x . ' = ' . ($nextToken - $x) . '\)</div>',
					true
				);
				$i++;
			} else {
				$tempValue = $x . (isset(self::$translateTableInverse[$x])
						? ' (' . self::$translateTableInverse[$x] . ')'
						: ''
					);

				$step->setTitle(
					$this->getTitleBasic($lastPosition, $i, $roman)
					. ($i === 0
						? 'Začneme hodnotou ' . $tempValue . ', kterou si vyhledáme v tabulce.'
						: 'Přičteme hodnotu ' . $tempValue . ' podle tabulky.'
					), true
				);
				$return += $x;
			}

			$step->setLatex((string) $return);
			$steps[] = $step;
			$lastPosition = $i;
		}

		$step = new Step();
		$step->setTitle('Řešení');
		$step->setLatex('\\text{' . $roman . '} \rightarrow ' . $return);

		$steps[] = $step;

		return $steps;
	}


	/**
	 * @param BigInteger|int|string $input
	 * @return Step[]
	 */
	public function getIntToRomanSteps($input): array
	{
		$int = BigInteger::of($input);

		$steps = [];

		if ($int->isLessThan(0)) {
			$step = new Step();
			$step->setTitle('Pravidlo pro záporná čísla');
			$step->setDescription('Pouze kladná čísla lze převádět na Římská čísla. Výpočet byl proto zastaven.');

			$steps[] = $step;

			return $steps;
		}

		$step = new Step();
		$step->setTitle('Převodní tabulka');
		$step->setDescription(
			'Ivan Vedl Xenii Lesem Cestou Do Města.' . $this->getTranslateTable(),
			true
		);

		$steps[] = $step;

		$step = new Step();
		$step->setTitle('Strategie');
		$step->setDescription(
			'<p>Při převodu čísla se budeme snažit najít vždy co nejvyšší možnou cifru, '
			. 'kterou můžeme najednou odečíst od aktuálního zbytku.</p>'
			. '<p>Při přepočtu nesmíme zapomenout zohlednit pravidlo pro odečítání '
			. '(například hodnota 4 se zapisuje jako \(\textrm{IV}\), protože \(5 - 1 = 4\))</p>'
			. '<p>Pro odečet se používá jen \(\textrm{I}\), \(\textrm{X}\) a \(\textrm{C}\).</p>'
			. '<p>Během výpočtu si budeme pamatovat aktuální zbytek.</p>',
			true
		);

		$steps[] = $step;

		$return = '';
		$iterator = 0;
		foreach (self::$romanNumber as $key => $val) {
			$repeat = $int->dividedBy($val, RoundingMode::FLOOR);
			if ($repeat->isGreaterThan(0)) {
				$return .= '\\' . ($val >= 5000
						? 'overline'
						: 'textrm'
					) . '{'
					. Strings::upper(str_repeat($key, $repeat->toInt()))
					. '}';

				$step = new Step();
				$step->setTitle(
					($iterator === 0
						? 'Začínáme s hodnotou'
						: 'Zbytek'
					) . ' <span style="color:black">' . $int . '</span>',
					true
				);
				$step->setDescription(
					'<p>' . ($iterator === 0
						? 'V prvním kroku najdeme nejvyšší číslo, které se vejde do hodnoty \(' . $int . '\), to je:'
						: 'Najdeme další nejvyšší číslo, které se vejde do zbytku \(' . $int . '\), to je:'
					) . '</p>'
					. '<div class="my-3 text-center">\(' . $repeat . '\cdot \textrm{' . $key . '}'
					. '\ \rightarrow\ ' . $repeat . '\cdot ' . $val
					. '\ =\ ' . ($repeat->multipliedBy($val)) . '\)</div>'
					. '<p>Odečteme z aktuální hodnoty nejnovější mezivýsledek '
					. 'a zapamatujeme si ho do dalšího kroku jako zbytek:</p>'
					. '<div class="my-3 text-center">\('
					. $int . ' - ' . ($repeat->multipliedBy($val)) . ' = ' . $int->minus($repeat->multipliedBy($val))
					. '\)</div>'
					. '<p>Zapíšeme mezivýsledek do celkového výsledku:</p>',
					true
				);
				$step->setLatex($return);

				$steps[] = $step;
				$iterator++;
			}
			$int = $int->mod($val);
		}

		$step = new Step();
		$step->setTitle('Řešení');
		$step->setDescription(
			'Zbytek má hodnotu nula, číslo je tedy převedeno.'
		);
		$step->setLatex($input . ' \rightarrow ' . $return);

		$steps[] = $step;

		return $steps;
	}


	private function getTitleBasic(int $lastPosition, int $currentPosition, string $roman): string
	{
		$return = '';
		$lastPosition = $currentPosition === 0 ? $lastPosition : $lastPosition + 1;

		for ($i = 0; isset($roman[$i]); $i++) {
			$return .=
				($i === $lastPosition ? '<span style="border-bottom:1px solid #dc3545;background:#ffd5d5">' : '')
				. $roman[$i]
				. ($i === $currentPosition ? '</span>' : '');
		}

		return '<span style="color:black">' . $return . '</span> | ';
	}


	private function getTranslateTable(string $roman = null): string
	{
		$return = '';
		foreach (self::$translateTable as $char => $int) {
			$useCount = $roman === null ? 0 : substr_count($roman, $char);
			$return .= '<tr>'
				. '<td class="text-center">\(\textrm{' . $char . '}\)</td>'
				. '<td>' . (self::$translateTableCzechHelp[$char] ?? '-') . '</td>'
				. '<td>' . $int . '</td>'
				. ($roman !== null ? '<td>' . ($useCount > 0 ? $useCount . '&times;' : 'Ne') . '</td>' : '')
				. '</tr>';
		}

		return '<table class="mt-3">'
			. '<tr>'
			. '<th class="text-center" style="width:48px">Znak</th>'
			. '<th class="text-center" style="width:64px">Pomůcka</th>'
			. '<th>Hodnota</th>'
			. ($roman !== null ? '<th style="width:64px">Použito?</th>' : '')
			. '</tr>'
			. $return
			. '</table>';
	}
}

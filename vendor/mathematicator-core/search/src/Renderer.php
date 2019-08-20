<?php

namespace Mathematicator\Search;

use Mathematicator\Search\Box;
use Mathematicator\Engine\MathematicatorException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Texy\Texy;

class Renderer
{

	/**
	 * @var Texy
	 */
	private $texy;

	public function __construct(Texy $texy)
	{
		$this->texy = $texy;
	}

	public function render($data, string $type): string
	{
		switch ($type) {
			case Box::TYPE_TEXT:
				return $this->renderText($data);
			case Box::TYPE_LATEX:
				return $this->renderLatex($data);
			case Box::TYPE_HTML:
				return $this->renderHtml($data);
			case Box::TYPE_TABLE:
				return $this->renderTable($data);
		}

		throw new MathematicatorException('Unknown box type "' . $type . '"');
	}

	public function renderTable(string $data): string
	{
		$return = '';

		foreach (\json_decode($data) as $row) {
			$return .= '<tr>';
			foreach ($row as $column) {
				if (Strings::startsWith($column, '!')) {
					$return .= '<th style="text-align:right;max-width:200px">' . preg_replace('/^!/', '', $column) . '</th>';
				} else {
					$return .= '<td>' . preg_replace_callback('/^(?<left>[=])?(?<content>.+?)(?<right>[=])?$/', function ($row) {
							if ($row['left'] === $row['right']) {
								switch ($row['left']) {
									case '=':
										return '<div style="text-align:center">' . $row['content'] . '</div>';
										break;

									default:
										return $row['content'];
								}
							} else {
								return $row[0];
							}
						}, $column) . '</td>';
				}
			}
			$return .= '</tr>';
		}

		return '<table>' . $return . '</table>';
	}

	public function renderTitle(string $title): string
	{
		$title = $title ?? 'Box bez názvu';
		$return = '';
		$iterator = 0;

		foreach (explode('|', $title) as $item) {
			$item = trim($item);
			if ($iterator > 0 && preg_match('/.+\:\s+.+/', $item, $itemParser)) {
				$return .= '<span class="search-box-header-hightlight">' . $item . '</span>';
			} else {
				$return .= '<span class="search-box-header-text">' . $item . '</span>';
			}

			$iterator++;
		}

		return $return;
	}

	private function renderText(string $data): string
	{
		return $this->texy->process($data);
	}

	private function renderLatex(string $data): string
	{
		$return = '';

		foreach (explode("\n", $data) as $line) {
			if (Validators::isNumeric($line)) {
				$return .= '<div>' . str_replace('\ ', '&nbsp;', $this->numberFormat($line)) . '</div>';
			} else {
				$return .= '<div>\(' . preg_replace_callback('/(-?\d*[.]?\d+)/', function ($number) {
						return $this->numberFormat($number[1]);
					}, $line) . '\)</div>';
			}
		}

		return $return;
	}

	/**
	 * @param string $number
	 * @param bool $isLookLeft
	 * @return string
	 */
	private function numberFormat(string $number, bool $isLookLeft = true): string
	{
		$return = null;

		if (\strlen($number) <= 3) {
			$return = $number;
		} elseif (preg_match('/^-?\d+\z/', $number)) {
			$return = '';

			if ($isLookLeft === true) {
				while (true) {
					if (preg_match('/^(\d+)(\d{3})$/', $number, $temp)) {
						$number = $temp[1];
						$return = $temp[2] . '\ ' . $return;
					} else {
						$return = $number . '\ ' . $return;
						break;
					}
				}
			} else {
				while (true) {
					if (preg_match('/^(\d{3})(\d+)$/', $number, $temp)) {
						$number = $temp[2];
						$return = $return . '\ ' . $temp[1];
					} else {
						$return = $return . '\ ' . $number;
						break;
					}
				}
			}
		} elseif (preg_match('/^0*(?<left>.+?)\.(?<right>.+?)0*$/', $number, $parser)) {
			$return = preg_replace('/\.0*$/', '',
				$this->numberFormat($parser['left'])
				. '.' . $this->numberFormat($parser['right'], false)
			);
		} else {
			$formattedNumber = preg_replace('/\.0+$/', '', number_format($number, 64, '.', ' '));

			$return = $formattedNumber === 'inf'
				? preg_replace('/(\d{3})/', '$1 ', $number)
				: $formattedNumber;
		}

		return $return === null ? $number : preg_replace('/(^\\\\\s*)|(\\\\\s*$)/', '', $return);
	}

	private function renderHtml(string $data): string
	{
		return $data;
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Martindilling\Sunny\Sunny;
use Mathematicator\Engine\Helper\Czech;
use Mathematicator\Engine\Source;
use Mathematicator\Search\Box;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Solaris\MoonPhase;

class DateController extends BaseController
{

	private static $months = [
		1 => 'leden', 'únor', 'březen', 'duben', 'květen',
		'červen', 'červenec', 'srpen', 'září', 'říjen',
		'listopad', 'prosinec',
	];

	private static $days = ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'];

	public function actionDefault(): void
	{
		$date = DateTime::from($this->getQuery());

		$this->setInterpret(
			Box::TYPE_HTML,
			Strings::firstUpper(self::$days[(int) $date->format('w')])
			. ' ' . Czech::getDate($date->getTimestamp())
			. ' ' . $date->format('H:i:s')
			. '<div class="text-right text-secondary" style="font-size:10pt">'
			. '(GMT+1) Informace pro Prahu, Česká republika'
			. '</div>'
		);

		$dates = $this->getDates((int) $date->format('Y'));

		$this->renderCalendar($date, $dates);
		$this->sun($date);
		$this->moon($date);
	}

	/**
	 * @param DateTime $date
	 * @param int[][][] $dates
	 */
	private function renderCalendar(DateTime $date, array $dates): void
	{
		$weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
		$weekdaysCzech = ['Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota', 'Neděle'];
		$buffer = '';
		$dayInt = (int) $date->format('d');
		$buffer .= '<table>'
			. '<tr><th class="text-center" style="width:14.28%">'
			. implode('</th><th class="text-center" style="width:14.28%">', $weekdaysCzech)
			. '</th></tr>';

		foreach ($dates[(int) $date->format('m')] as $week => $days) {
			$buffer .= '<tr>';
			foreach ($weekdays as $day) {
				if (isset($days[$day]) && $days[$day] === $dayInt) {
					$buffer .= '<th class="text-center" style="background:#28a745;color:white">'
						. ($days[$day] ?? '-&nbsp')
						. '</th>';
				} else {
					$buffer .= '<td class="text-center">' . ($days[$day] ?? '&nbsp') . '</td>';
				}
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</table>';

		$this->addBox(Box::TYPE_HTML)
			->setTitle(
				'Kalendář | Zobrazený měsíc: '
				. Strings::firstUpper(self::$months[(int) $date->format('m')])
				. ' ' . $date->format('Y')
			)->setText($buffer);
	}

	/**
	 * @param int $year
	 * @return int[][][]
	 */
	private function getDates(int $year): array
	{
		$dates = [];

		date('L', mktime(0, 0, 0, 7, 7, $year)) ? $days = 366 : $days = 365;
		for ($i = 1; $i <= $days; $i++) {
			$month = (int) date('m', mktime(0, 0, 0, 1, $i, $year));
			$wk = (int) date('W', mktime(0, 0, 0, 1, $i, $year));
			$wkDay = date('D', mktime(0, 0, 0, 1, $i, $year));
			$day = (int) date('d', mktime(0, 0, 0, 1, $i, $year));

			$dates[$month][$wk][$wkDay] = $day;
		}

		return $dates;
	}

	private function sun(DateTime $date): void
	{
		$day = new Sunny($date, 'Europe/Prague');
		$day->setLocation(50.0755381, 14.4378005); // Prague

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Slunce | Vypočítáno pro: Praha [50.0755381, 14.4378005]')
			->setKeyValue([
				'Poloha' => $day->latitude . ', ' . $day->longitude . ' (zenit: ' . $day->zenith . ')',
				'Východ slunce' => $day->getSunrise(),
				'Západ slunce' => $day->getSunset(),
			]);

		$source = new Source(
			'Konfigurace slunce',
			'https://github.com/martindilling/Sunny',
			'Otevřená knihovna pro práci s datem a časem.'
		);
		$source->setAuthor('Martin Dilling-Hansen');

		$this->addSource($source);
	}

	private function moon(DateTime $date): void
	{
		$moon = new MoonPhase($date->getTimestamp());

		$phaseTable = '';
		$phaseData = [
			'Nov' => date('d. m. Y', (int) $moon->getNewMoon())
				. ' (další: ' . date('d. m. Y', (int) $moon->getNextNewMoon()) . ')',
			'První čtvrť' => date('d. m. Y', (int) $moon->getFirstQuarter())
				. ' (další: ' . date('d. m. Y', (int) $moon->getNextFirstQuarter()) . ')',
			'Úplněk' => date('d. m. Y', (int) $moon->getFullMoon())
				. ' (další: ' . date('d. m. Y', (int) $moon->getNextFullMoon()) . ')',
			'Poslední čtvrť' => date('d. m. Y', (int) $moon->getLastQuarter())
				. ' (další: ' . date('d. m. Y', (int) $moon->getNextLastQuarter()) . ')',
		];

		foreach ($phaseData as $phaseKey => $phaseValue) {
			$phaseTable .= '<tr>'
				. '<th>' . $phaseKey . ':</th>'
				. '<td>' . $phaseValue . '</td>'
				. '</tr>';
		}

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Měsíc')
			->setKeyValue([
				'Fáze' => number_format($moon->getPhaseRatio() * 100, 2) . ' % '
					. '(0 % = Nov; 50 % = Úplněk; 100 % = Poslední čtvrť)'
					. '<br>' . $moon->phaseName()
					. '<table class="mt-3">' . $phaseTable . '</table>',
				'Úroveň osvětlení' => number_format($moon->illumination() * 100, 2) . ' %',
				'Počet dnů od Novu' => number_format($moon->getAge(), 2),
				'Úhlová velikost' => number_format($moon->getDiameter(), 4) . '°',
				'Vzdálenost od Země' => '~ ' . number_format($moon->getDistance(), 0, '.', ' ') . ' km',
				'Vzdálenost od Slunce' => '~ ' . number_format($moon->getSunDistance(), 0, '.', ' ') . ' km '
					. '(' . number_format($moon->getSunDiameter() * 100, 2) . ' %)',
			]);

		$source = new Source(
			'Fáze měsíce',
			'http://aa.usno.navy.mil/faq/docs/moon_phases.php',
			'Informace o fázích měsíce a Keplerovy rovnice.'
		);
		$source->setAuthor('Antonio Cidadao');

		$this->addSource($source);
	}

}
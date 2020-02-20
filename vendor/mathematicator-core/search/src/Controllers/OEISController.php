<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Baraja\Doctrine\EntityManagerException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Mathematicator\Engine\Source;
use Mathematicator\Search\Box;
use Mathematicator\Statistics\StatisticsManager;
use Nette\Application\LinkGenerator;
use Nette\Utils\Strings;

class OEISController extends BaseController
{

	/**
	 * @var string[]
	 */
	private static $types = [
		'O' => 'Offset',
		'K' => 'Klíčová slova',
		'A' => 'Autor',
	];

	/**
	 * @var StatisticsManager
	 */
	private $statisticManager;

	/**
	 * @param LinkGenerator $linkGenerator
	 * @param StatisticsManager $statisticManager
	 */
	public function __construct(
		LinkGenerator $linkGenerator,
		StatisticsManager $statisticManager
	)
	{
		parent::__construct($linkGenerator);
		$this->statisticManager = $statisticManager;
	}

	public function actionDefault(): void
	{
		$this->setInterpret(Box::TYPE_HTML)
			->setText(
				'<a href="https://oeis.org/' . $this->getQuery() . '" target="_blank">'
				. $this->getQuery()
				. '</a>'
				. '<br>On-line Encyklopedie celočíselných posloupností'
			);

		try {
			$sequence = $this->statisticManager->getSequence($this->getQuery());
		} catch (NoResultException|NonUniqueResultException|EntityManagerException $e) {
			return;
		}

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Posloupnost')
			->setText(implode(', ', $sequence->getSequence()) . ', ...');

		$formula = $sequence->getDataType('F');

		if ($formula !== null) {
			$this->addBox(Box::TYPE_HTML)
				->setTitle('Předpis')
				->setText($this->formatHr($formula));
		}

		$example = $sequence->getDataType('e');

		if ($example !== null) {
			$this->addBox(Box::TYPE_HTML)
				->setTitle('Příklad')
				->setText($this->formatBr($example));
		}

		$comment = $sequence->getDataType('C');

		if ($comment !== null) {
			$this->addBox(Box::TYPE_HTML)
				->setTitle('Komentář')
				->setText($this->formatBr($comment));
		}

		$author = $sequence->getDataType('A');

		if ($author) {
			$source = new Source(
				'OEIS',
				'https://oeis.org/' . $sequence->getAId(),
				'On-line Encyklopedie celočíselných posloupností.'
			);
			$source->setAuthor($author);
			$this->addSource($source);
		}

		foreach (self::$types as $type => $label) {
			$data = $sequence->getDataType($type);

			if ($data !== null) {
				$this->addBox(Box::TYPE_HTML)
					->setTitle($label)
					->setText(str_replace("\n", '<hr>', $this->formatLinks(htmlspecialchars($data))));
			}
		}
	}

	/**
	 * @param string $data
	 * @return string
	 */
	private function formatBr(string $data): string
	{
		$return = '';
		$lastPre = false;

		foreach (explode("\n", $this->formatLinks(htmlspecialchars($data))) as $line) {
			if (Strings::contains($line, '   ')) {
				$return .= ($lastPre ? '' : '<pre class="p-2 my-2" style="border:1px solid #aaa">') . $line . "\n";
				$lastPre = true;
			} else {
				$return .= ($lastPre ? '</pre>' : '') . '<p>' . $line . '</p>';
				$lastPre = false;
			}
		}

		return $return;
	}

	/**
	 * @param string $data
	 * @return string
	 */
	private function formatHr(string $data): string
	{
		return '<div class="text-center p-2 mt-2" style="background:#eee">'
			. str_replace("\n", '<hr>', $this->formatLinks(htmlspecialchars($data)))
			. '</div>';
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
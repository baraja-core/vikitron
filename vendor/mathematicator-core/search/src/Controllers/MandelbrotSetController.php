<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\InvalidBoxException;
use Mathematicator\Engine\Source;
use Mathematicator\Engine\TerminateException;
use Mathematicator\MandelbrotSet\MandelbrotSet;
use Mathematicator\MandelbrotSet\MandelbrotSetRequest;
use Mathematicator\Search\Box;

class MandelbrotSetController extends BaseController
{

	/**
	 * @var MandelbrotSet
	 * @inject
	 */
	public $mandelbrotSet;

	/**
	 * @throws InvalidBoxException|TerminateException
	 */
	public function actionDefault(): void
	{
		$this->setInterpret(Box::TYPE_KEYWORD, 'Mandelbrotova množina komplexních čísel');

		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Definice')
			->setText(
				'Mandelbrotova množina je množina bodů komplexní roviny, které jsou odvozeny od rekurzivních procesů '
				. 's komplexními čísly patřícími této množině a jejímu okolí. Mandelbrotova množina je jeden '
				. 'z nejznámějších fraktálů, přesněji řečeno fraktálem je její okraj. K jejímu určení se používá '
				. 'zobrazení, které každému komplexnímu číslu \(\displaystyle c\) přiřazuje určitou posloupnost '
				. 'komplexních čísel \(\displaystyle z_{n}\). Tato posloupnost je určena rekurzivním předpisem.'
			);

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Rekurzivní předpis | Iterační pravidlo')
			->setText(
				'z_{n+1}\ =\ z^2_n\ +\ c' . "\n"
				. 'z_0\ =\ 0'
			);

		$config = $this->getDynamicConfiguration('mandelbrot-set')
			->setTitle('Nastavení hodnot')
			->addLabel('deltaA', 'Delta A')
			->addLabel('deltaB', 'Delta B')
			->setValues([
				'deltaA' => 3,
				'deltaB' => 4,
			]);

		$this->addBoxDynamicConfiguration('mandelbrot-set');

		$image = $this->mandelbrotSet->loadImage(
			new MandelbrotSetRequest(
				(int) $config->getValue('deltaA', '2'),
				(int) $config->getValue('deltaB', '2'),
				700, 500
			)
		);

		$this->addBox(Box::TYPE_IMAGE)
			->setTitle('Vizualizace')
			->setText($image);

		$this->addSource(new Source(
			'Generátor obrázků Mandelbrotovy množiny',
			'https://github.com/mathematicator-core/mandelbrot-set',
			'Veřejně dostupná opensource knihovna.'
		));
		$this->addSource(new Source(
			'Pavol Hejný',
			'https://www.pavolhejny.com/',
			'Autor implementace grafického generátoru a konzultace s nasazením do vyhledávání.'
		));
		$this->addSource(new Source(
			'Definice na Wikipedii',
			'https://cs.wikipedia.org/wiki/Mandelbrotova_mno%C5%BEina'
		));
	}

}
<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\Box;
use Mathematicator\Engine\Controller\BaseController;

final class CrossMultiplicationController extends BaseController
{
	public function actionDefault(): void
	{
		$this->setInterpret(Box::TYPE_HTML)
			->setText('Trojčlenka');

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Definice')
			->setText(
				'<b>Trojčlenka</b> je mechanický matematický postup používaný '
				. 'při výpočtech založených na přímé a nepřímé úměrnosti.'
			);

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Výpočet')
			->setText(
				'<p>Zadejte 3 libovolné hodnoty, čtvrtá bude vypočítána.</p>

				<div class="row">
					<div class="col">
						<input class="form-control form-control-sm">
					</div>
					<div class="col-1 text-center">
						.........
					</div>
					<div class="col">
						<input class="form-control form-control-sm text-right">
					</div>
				</div>
				<div class="row mt-2">
					<div class="col">
						<input class="form-control form-control-sm">
					</div>
					<div class="col-1 text-center">
						.........
					</div>
					<div class="col">
						<input class="form-control form-control-sm text-right">
					</div>
				</div>
				<div class="row mt-2 mb-4 pb-3" style="border-bottom: 1px solid black">
					<div class="col-5">
						<select class="form-control">
							<option>Přímá úměra</option>
							<option>Nepřímá úměra</option>
						</select>
					</div>
					<div class="col"></div>
					<div class="col-5 text-right">
						<input class="btn btn-primary" value="Vypočítat">
					</div>
				</div>

				<p>x / 465 = 28 / 30</p>
				<p>30x = 465</p>
				
				'
			);
	}
}
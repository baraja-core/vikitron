<?php

declare(strict_types=1);

namespace Mathematicator\Search\Controller;


use Mathematicator\Engine\Controller\BaseController;
use Mathematicator\Engine\Entity\Box;

final class CrossMultiplicationController extends BaseController
{
	public function actionDefault(): void
	{
		$this->setInterpret(Box::TYPE_HTML)
			->setText($this->translator->translate('search.crossMultiplication.title'));

		$this->addBox(Box::TYPE_HTML)
			->setTitle($this->translator->translate('search.definition'))
			->setText($this->translator->translate('search.crossMultiplication.description'));

		$this->addBox(Box::TYPE_HTML)
			->setTitle($this->translator->translate('search.calculation'))
			->setText(
				'<p>' . $this->translator->translate('search.crossMultiplication.enter3Vals') . '</p>

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
							<option>' . $this->translator->translate('search.crossMultiplication.directProportionality') . '</option>
							<option>' . $this->translator->translate('search.crossMultiplication.indirectProportionality') . '</option>
						</select>
					</div>
					<div class="col"></div>
					<div class="col-5 text-right">
						<input class="btn btn-primary" value="' . $this->translator->translate('search.calculate') . '">
					</div>
				</div>

				<p>x / 465 = 28 / 30</p>
				<p>30x = 465</p>

				'
			);
	}
}

<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Entity\Box;

final class OtherController extends BaseController
{
	public function actionDefault(): void
	{
		$this->addBox(Box::TYPE_HTML)
			->setTitle('&nbsp;')
			->setText(
				'<div style="padding:1em;background:#FEFEFE">'
				. '<h1>' . $this->translator->translate('engine.ohNo') . '</h1>'
				. '<p>' . $this->translator->translate('engine.contentSearchFailed') . '</p>'
				. '<div style="text-align:center;padding:4em 1em">'
				. '<img src="https://mathematicator.com/img/error_dinosaur.gif" alt="' . $this->translator->translate('engine.contentSearchFailed') . '">'
				. '</div>'
				. '</div>'
			)
			->setIcon('fas fa-exclamation-triangle')
			->setTag('no-results');

		$this->terminate();
	}
}

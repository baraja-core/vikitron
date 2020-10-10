<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Query;
use Nette\Utils\Strings;

final class ErrorTooLongController extends BaseController
{
	public function actionDefault(): void
	{
		$this->addBox(Box::TYPE_TEXT)
			->setTitle($this->translator->translate('engine.error.tooLongQuery.title'))
			->setText(
				'<p>' . $this->translator->translate('engine.error.tooLongQuery.p1', ['lengthLimit' => Query::LENGTH_LIMIT, 'inputLength' => Strings::length($this->getQuery())]) . '</p>'
				. '<p>' . $this->translator->translate('engine.error.tooLongQuery.p2') . '</p>'
				. '<p>' . $this->translator->translate('engine.error.tooLongQuery.p3') . '</p>'
			)
			->setIcon('fas fa-exclamation-triangle')
			->setTag('no-results');
	}
}

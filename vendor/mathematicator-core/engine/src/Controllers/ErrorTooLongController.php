<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Box;
use Nette\Utils\Strings;

class ErrorTooLongController extends BaseController
{
	public function actionDefault(): void
	{
		$this->addBox(Box::TYPE_TEXT)
			->setTitle('Příliš dlouhý dotaz')
			->setText('Maximální délka vstupního dotazu je momentálně omezena na 1024 znaků (vloženo ' . Strings::length($this->getQuery()) . ' znaků) v kódování UTF-8.
			
			Toto omezení nasazujeme z výkonnostních důvodů.
			
			Pokud potřebujete vykonávat náročnější výpočty, kontaktujte nás.')
			->setIcon('fas fa-exclamation-triangle')
			->setTag('no-results');
	}
}

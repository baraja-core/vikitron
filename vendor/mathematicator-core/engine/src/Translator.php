<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Nette\Localization\ITranslator;

class Translator implements ITranslator
{

	public function translate($message, ...$parameters): string
	{
		return (string) $message;
	}

}
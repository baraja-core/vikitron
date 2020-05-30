<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\Box;
use Mathematicator\Engine\Controller\BaseController;
use Mathematicator\Tokenizer\Tokenizer;

final class TreeController extends BaseController
{

	/**
	 * @var Tokenizer
	 * @inject
	 */
	public $tokenizer;


	public function actionDefault(): void
	{
		preg_match('/^(?:strom|tree)\s+(.+)$/', $this->getQuery(), $parser);

		$tokens = $this->tokenizer->tokenize($parser[1]);
		$objects = $this->tokenizer->tokensToObject($tokens);

		$this->setInterpret(Box::TYPE_LATEX, $this->tokenizer->tokensToLatex($objects));

		$this->addBox(Box::TYPE_HTML)
			->setTitle('Interní interpretace dotazu ve stromu')
			->setText($this->tokenizer->renderTokensTree($objects));
	}
}
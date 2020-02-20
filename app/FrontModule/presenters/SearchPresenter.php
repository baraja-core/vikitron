<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;


use Mathematicator\Search\Renderer;
use Mathematicator\Tokenizer\Tokenizer;
use Tracy\Debugger;

class SearchPresenter extends BasePresenter
{

	/**
	 * @var Tokenizer
	 * @inject
	 */
	public $tokenizer;

	/**
	 * @var Renderer
	 * @inject
	 */
	public $renderer;

	public function actionDefault(string $q = ''): void
	{
		$this->template->searchQuery = $q;
		$this->template->result = $this->search->get()->search($q);
		$this->template->renderer = $this->renderer;
	}

	public function actionParser($q): void
	{
		Debugger::$maxDepth = 8;

		if (!$q) {
			$q = '5+3*256-(x^24/sin(x-4))';
		}

		$tokens = $this->tokenizer->tokenize($q);
		$objects = $this->tokenizer->tokensToObject($tokens);
		dump($objects);

		$this->template->q = $q;
		$this->template->tokens = $tokens;
		$this->template->objects = $objects;
	}

}
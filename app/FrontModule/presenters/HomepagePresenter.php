<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;


use Mathematicator\Search\Renderer;
use Mathematicator\Search\SearchAccessor;

class HomepagePresenter extends BasePresenter
{

	/**
	 * @var SearchAccessor
	 * @inject
	 */
	public $search;

	/**
	 * @var Renderer
	 * @inject
	 */
	public $renderer;

	/**
	 * @param string|null $q
	 */
	public function actionDefault(?string $q = null): void
	{
		$this->template->query = $q;
		$this->template->result = $q !== null && $q !== '' ? $this->search->get()->search($q) : null;
		$this->template->renderer = $this->renderer;
	}

}

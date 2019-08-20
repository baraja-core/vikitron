<?php

namespace App\Presenters;


use Nette\Application\BadRequestException;
use Nette\Application\Request;

class Error4xxPresenter extends BasePresenter
{

	public function startup(): void
	{
		parent::startup();

		if ($this->getRequest() !== null && !$this->getRequest()->isMethod(Request::FORWARD)) {
			$this->error();
		}
	}

	public function renderDefault(BadRequestException $exception): void
	{
		$file = __DIR__ . '/templates/Error/' . $exception->getCode() . '.latte';
		$file = is_file($file) ? $file : __DIR__ . '/templates/Error/4xx.latte';
		$this->template->setFile($file);
		$this->template->error = $exception->getMessage();
	}

}

<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Search\Box;
use Nette\Application\LinkGenerator;
use Nette\Http\Request;

class OtherController extends BaseController
{

	/**
	 * @var string
	 */
	private $basePath;

	/**
	 * @param LinkGenerator $linkGenerator
	 * @param Request $httpRequest
	 */
	public function __construct(LinkGenerator $linkGenerator, Request $httpRequest)
	{
		parent::__construct($linkGenerator);
		$baseUri = $httpRequest ? rtrim($httpRequest->getUrl()->getBaseUrl(), '/') : null;
		$this->basePath = preg_replace('#https?://[^/]+#A', '', $baseUri);
	}

	public function actionDefault(): void
	{
		$this->addBox(Box::TYPE_HTML)
			->setTitle('&nbsp;')
			->setText(
				'<div style="padding: 1em; background: #FEFEFE">'
				. '<h1>Ale ne!</h1>'
				. '<p>Obsah se nepodařilo vyhledat…</h1>'
				. '<div style="text-align: center; padding: 4em 1em;">'
				. '<img src="' . $this->basePath . '/img/error_dinosaur.gif" alt="Content does not found">'
				. '</div>'
				. '</div>'
			)
			->setIcon('fas fa-exclamation-triangle')
			->setTag('no-results');
	}

}

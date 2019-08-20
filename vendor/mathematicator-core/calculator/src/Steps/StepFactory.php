<?php

namespace Model\Math\Step;

use Mathematicator\Calculator\Step;
use Nette\Application\LinkGenerator;
use Nette\Utils\Json;

class StepFactory
{

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;

	public function __construct(LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param string|null $title
	 * @param string|null $latex
	 * @param string|null $description
	 * @return Step
	 */
	public function create(?string $title = null, ?string $latex = null, ?string $description = null): Step
	{
		return new Step($title, $latex, $description);
	}

	/**
	 * @param string $type
	 * @param $data
	 * @return string
	 */
	public function getAjaxEndpoint(string $type, $data): string
	{
		return $this->linkGenerator->link('Front:Search:step', [
			'type' => $type,
			'data' => Json::encode($data),
		]);
	}

}

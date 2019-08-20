<?php

namespace Model\Math\Step;

use Mathematicator\Engine\TerminateException;
use Model\Math\Step\Controller\IStepController;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;

class StepEndpoint
{

	/**
	 * @var StepFactory
	 */
	private $stepFactory;

	/**
	 * @var Container
	 */
	private $serviceFactory;

	/**
	 * @var IStepController
	 */
	private $callback;

	/**
	 * @param StepFactory $stepFactory
	 * @param Container $container
	 */
	public function __construct(StepFactory $stepFactory, Container $container)
	{
		$this->stepFactory = $stepFactory;
		$this->serviceFactory = $container;
	}

	/**
	 * @param string $type
	 * @param string $data
	 * @return ArrayHash[]
	 */
	public function getStep(string $type, string $data): array
	{
		$this->callback = $this->serviceFactory->getByType($type);

		try {
			$data = Json::decode($data);
			$arrayHash = new ArrayHash();
			foreach ($data as $k => $v) {
				$arrayHash->{$k} = $v;
			}
			$steps = $this->callback->actionDefault($arrayHash);
		} catch (TerminateException $e) {
			$step = $this->stepFactory->create();
			$step->setTitle('Nepodařilo se najít postup pro [' . $type . ']');
			$steps[] = $step;
		}

		$return = [];

		foreach ($steps as $step) {
			$return[] = [
				'title' => $step->getTitle(),
				'latex' => $step->getLatex(),
				'description' => $step->getDescription(),
				'ajaxEndpoint' => $step->getAjaxEndpoint(),
			];
		}

		return $return;
	}

}

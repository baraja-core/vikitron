<?php

declare(strict_types=1);

namespace Mathematicator\Step;


use Mathematicator\Engine\TerminateException;
use Mathematicator\Step\Controller\IStepController;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;

final class StepEndpoint
{

	/** @var StepFactory */
	private $stepFactory;

	/** @var Container */
	private $serviceFactory;

	/** @var IStepController */
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
	 * @return mixed[]
	 */
	public function getStep(string $type, string $data): array
	{
		$this->callback = $this->serviceFactory->getByType($type);

		try {
			$data = json_decode($data);
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

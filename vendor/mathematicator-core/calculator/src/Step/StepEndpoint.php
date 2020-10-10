<?php

declare(strict_types=1);

namespace Mathematicator\Calculator\Step;


use Mathematicator\Calculator\Step\Controller\IStepController;
use Mathematicator\Engine\Exception\TerminateException;
use Mathematicator\Engine\Step\Step;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;

final class StepEndpoint
{

	/** @var Container */
	private $container;


	public function __construct(Container $container)
	{
		$this->container = $container;
	}


	/**
	 * @return mixed[]
	 */
	public function getStep(string $type, string $data): array
	{
		$callback = $this->container->getByType($type);
		if (!$callback instanceof IStepController) {
			throw new \RuntimeException('Service must be instance of "' . IStepController::class . '", but type "' . $type . '" given.');
		}

		try {
			$data = \json_decode($data);
			$arrayHash = new ArrayHash();
			foreach ($data as $k => $v) {
				$arrayHash->{$k} = $v;
			}
			$steps = $callback->actionDefault($arrayHash);
		} catch (TerminateException $e) {
			$steps[] = StepFactory::addStep('Could not find a step-by-step solution for "' . $type . '".');
		}

		$return = [];
		foreach ($steps as $step) {
			/** @var Step $step */
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

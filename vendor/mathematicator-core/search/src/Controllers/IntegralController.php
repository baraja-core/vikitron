<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\InvalidBoxException;
use Mathematicator\Engine\MathematicatorException;
use Mathematicator\Engine\TerminateException;
use Mathematicator\Integral\IntegralSolver;
use Mathematicator\Search\Box;
use Mathematicator\Tokenizer\Tokenizer;
use Nette\Tokenizer\Exception;

class IntegralController extends BaseController
{

	/**
	 * @var IntegralSolver
	 * @inject
	 */
	public $integral;

	/**
	 * @var Tokenizer
	 * @inject
	 */
	public $tokenizer;

	/**
	 * @throws InvalidBoxException|MathematicatorException|TerminateException|Exception
	 */
	public function actionDefault(): void
	{
		preg_match('/^integr(?:a|á)l\s+(.+)$/u', $this->getQuery(), $parser);

		$process = $this->integral->process($parser[1] ?? $this->getQuery());
		$this->setInterpret(Box::TYPE_LATEX, $process->getQueryLaTeX());

		$resultTokens = $this->tokenizer->tokenize($process->getResult());
		$resultObjects = $this->tokenizer->tokensToObject($resultTokens);

		$this->addBox(Box::TYPE_LATEX)
			->setTitle('Řešení integrálu')
			->setText($this->tokenizer->tokensToLatex($resultObjects))
			->setSteps($process->getSteps());
	}

}
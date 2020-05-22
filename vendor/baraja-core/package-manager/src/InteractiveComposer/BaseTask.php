<?php

declare(strict_types=1);

namespace Baraja\PackageManager\Composer;


use Baraja\PackageManager\Helpers;
use Baraja\PackageManager\PackageRegistrator;

abstract class BaseTask implements ITask
{

	/** @var PackageRegistrator */
	protected $packageRegistrator;


	final public function __construct(PackageRegistrator $packageRegistrator)
	{
		$this->packageRegistrator = $packageRegistrator;
	}


	/**
	 * @param string $question
	 * @param string[] $possibilities
	 * @return string|null
	 */
	public function ask(string $question, array $possibilities = []): ?string
	{
		return Helpers::terminalInteractiveAsk($question, $possibilities);
	}
}
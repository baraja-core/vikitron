<?php

declare(strict_types=1);

namespace Mathematicator\Engine;


use Mathematicator\Search\Box;

final class SampleModule extends BaseModule
{

	/**
	 * @param string $query
	 * @return bool
	 */
	public function match(string $query): bool
	{
		return $query === 'help';
	}

	public function actionDefault(): void
	{
		$this->result->addBox(
			(new Box(Box::TYPE_TEXT))
				->setTitle('Help')
				->setText('What can I help you with?')
		);
	}

}
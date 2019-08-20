<?php

declare(strict_types=1);

namespace App\Presenters;


use Baraja\Doctrine\EntityManager;
use Nette;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var EntityManager
	 * @inject
	 */
	public $entityManager;

}

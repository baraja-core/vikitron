<?php

namespace App\Presenters;


use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IPresenter;
use Nette\Application\Request;
use Nette\Application\Responses;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\SmartObject;
use Tracy\ILogger;

class ErrorPresenter implements IPresenter
{

	use SmartObject;

	/**
	 * @var ILogger
	 */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * @param Request $request
	 * @return Responses\CallbackResponse|Responses\ForwardResponse
	 */
	public function run(Request $request): \Nette\Application\IResponse
	{
		$e = $request->getParameter('exception');

		if ($e instanceof BadRequestException) {
			list($module, , $sep) = Helpers::splitName($request->getPresenterName());
			$errorPresenter = $module . $sep . 'Error4xx';

			return new Responses\ForwardResponse($request->setPresenterName($errorPresenter));
		}

		$this->logger->log($e, ILogger::EXCEPTION);

		return new Responses\CallbackResponse(function (IRequest $httpRequest, IResponse $httpResponse) {
			if (preg_match('#^text/html(?:;|$)#', $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error/500.phtml';
			}
		});
	}

}

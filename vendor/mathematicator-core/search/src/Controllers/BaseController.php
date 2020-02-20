<?php

declare(strict_types=1);

namespace Mathematicator\SearchController;


use Mathematicator\Engine\InvalidBoxException;
use Mathematicator\Engine\Source;
use Mathematicator\Engine\TerminateException;
use Mathematicator\Search\Box;
use Mathematicator\Search\Context;
use Mathematicator\Search\DynamicConfiguration;
use Mathematicator\Search\Query;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\SmartObject;
use Tracy\Debugger;

/**
 * @property-read string $query
 * @property-read Query $queryEntity
 */
class BaseController implements IController
{

	use SmartObject;

	/**
	 * @var Context
	 */
	private $context;

	/**
	 * @var LinkGenerator
	 */
	private $linkGenerator;

	/**
	 * @param LinkGenerator $linkGenerator
	 */
	public function __construct(LinkGenerator $linkGenerator)
	{
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @return Context
	 */
	public function getContext(): Context
	{
		return $this->context;
	}

	/**
	 * @param string $type
	 * @return Box
	 * @throws TerminateException|InvalidBoxException
	 */
	public function addBox(string $type): Box
	{
		return $this->context->addBox($type);
	}

	/**
	 * @param string $key
	 * @throws InvalidBoxException
	 * @throws TerminateException
	 */
	public function addBoxDynamicConfiguration(string $key): void
	{
		$configuration = $this->getDynamicConfiguration($key);

		$content = '';
		$form = '';

		foreach ($configuration->getValues() as $valueKey => $value) {
			$content .= '<tr>';
			$content .= '<th>' . htmlspecialchars($configuration->getLabel($valueKey)) . '</th>';
			$content .= '<td><input type="text" '
				. 'name="' . htmlspecialchars($key . '_' . $valueKey) . '" '
				. 'value="' . htmlspecialchars((string) $value) . '" '
				. 'class="form-control"></td>';
			$content .= '</tr>';
			unset($_GET[$key . '_' . $valueKey]);
		}

		foreach ($_GET as $getKey => $getValue) {
			$form .= '<input type="hidden" '
				. 'name="' . htmlspecialchars((string) $getKey) . '" '
				. 'value="' . htmlspecialchars((string) $getValue) . '">';
		}

		if (isset($_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'])) {
			$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
				. '://' . preg_replace('/\?.*$/', '', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		} else {
			$currentUrl = '#';
		}

		$this->addBox(Box::TYPE_HTML)
			->setTitle($configuration->getTitle() ?? '')
			->setText(
				'<form action="' . $currentUrl . '" method="get">' . $form . '<table>'
				. $content
				. '</table>'
				. '<input type="submit" value="Použít" class="btn btn-primary mt-2">'
				. '</form>'
			);
	}

	/**
	 * @param string $boxType
	 * @param string|null $content
	 * @return Box
	 */
	public function setInterpret(string $boxType, ?string $content = null): Box
	{
		return $this->context->setInterpret($boxType, $content);
	}

	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->context->getQuery();
	}

	/**
	 * @internal
	 * @param Query $query
	 * @return Context
	 */
	public function createContext(Query $query): Context
	{
		if ($this->context === null) {
			$this->context = new Context($query);

			// Set dynamic configuration from user
			foreach ($_GET ?? [] as $getKey => $getValue) {
				if (preg_match('/^([a-z0-9-]+)_(.+)$/', $getKey, $parseKey)) {
					$this->context->getDynamicConfiguration($parseKey[1])->setValue($parseKey[2], $getValue);
				}
			}
		}

		return $this->context;
	}

	/**
	 * @return Query
	 */
	public function getQueryEntity(): Query
	{
		return $this->context->getQueryEntity();
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function actionDefault(): void
	{
		throw new \InvalidArgumentException(__METHOD__ . ': Method actionDefault() does not found in result Entity.');
	}

	/**
	 * @param string $query
	 * @return string
	 */
	public function linkToSearch(string $query): string
	{
		try {
			return $this->linkGenerator->link('Front:Search:default', [
				'q' => $query,
			]);
		} catch (InvalidLinkException $e) {
			Debugger::log($e);

			return '#invalid-link';
		}
	}

	/**
	 * @param string $key
	 * @return DynamicConfiguration
	 */
	public function getDynamicConfiguration(string $key): DynamicConfiguration
	{
		return $this->context->getDynamicConfiguration($key);
	}

	/**
	 * @param Source $source
	 */
	public function addSource(Source $source): void
	{
		$this->context->addSource($source);
	}

}

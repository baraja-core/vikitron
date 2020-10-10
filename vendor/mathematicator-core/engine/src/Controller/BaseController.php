<?php

declare(strict_types=1);

namespace Mathematicator\Engine\Controller;


use Mathematicator\Engine\Entity\Box;
use Mathematicator\Engine\Entity\Context;
use Mathematicator\Engine\Entity\DynamicConfiguration;
use Mathematicator\Engine\Entity\Query;
use Mathematicator\Engine\Entity\Source;
use Mathematicator\Engine\Exception\TerminateException;
use Mathematicator\Engine\Translator;

abstract class BaseController implements Controller
{

	/**
	 * @var Translator
	 * @inject
	 */
	public $translator;

	/** @var Context */
	private $context;


	final public function getContext(): Context
	{
		return $this->context;
	}


	final public function addBox(string $type): Box
	{
		try {
			return $this->context->addBox($type);
		} catch (\Throwable $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}
	}


	final public function addBoxDynamicConfiguration(string $key): void
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
				. '<input type="submit" value="' . $this->translator->translate('engine.button.apply') . '" class="btn btn-primary mt-2">'
				. '</form>'
			);
	}


	final public function setInterpret(string $boxType, ?string $content = null): Box
	{
		return $this->context->setInterpret($boxType, $content);
	}


	final public function getQuery(): string
	{
		return $this->context->getQuery();
	}


	/**
	 * Create new context. If context already exist rewrite existing.
	 *
	 * @internal
	 */
	final public function createContext(Query $query): Context
	{
		$this->context = new Context($query);

		// Set dynamic configuration from user
		foreach ($_GET ?? [] as $getKey => $getValue) {
			if (preg_match('/^([a-zA-Z0-9-]+)_(.+)$/', $getKey, $parseKey)) {
				$this->context->getDynamicConfiguration($parseKey[1])->setValue($parseKey[2], $getValue);
			}
		}

		return $this->context;
	}


	final public function getQueryEntity(): Query
	{
		return $this->context->getQueryEntity();
	}


	final public function linkToSearch(string $query): string
	{
		return $this->context->link($query);
	}


	final public function getDynamicConfiguration(string $key): DynamicConfiguration
	{
		return $this->context->getDynamicConfiguration($key);
	}


	final public function addSource(Source $source): void
	{
		$this->context->addSource($source);
	}


	/**
	 * @throws TerminateException
	 */
	final public function terminate(): void
	{
		throw new TerminateException('Automatically terminated by "' . \get_class($this) . '".');
	}
}

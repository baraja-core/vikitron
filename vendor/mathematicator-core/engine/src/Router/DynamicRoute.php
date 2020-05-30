<?php

declare(strict_types=1);

namespace Mathematicator\Router;


final class DynamicRoute
{
	public const TYPE_REGEX = 'regex';

	public const TYPE_STATIC = 'static';

	public const TYPE_TOKENIZE = 'tokenize';

	/** @var string */
	private $type;

	/** @var mixed */
	private $haystack;

	/** @var string */
	private $controller;


	/**
	 * @param string $type
	 * @param mixed $haystack
	 * @param string $controller
	 */
	public function __construct(string $type, $haystack, string $controller)
	{
		$this->type = $type;
		$this->haystack = $haystack;
		$this->controller = $controller;
	}


	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}


	/**
	 * @return mixed
	 */
	public function getHaystack()
	{
		return $this->haystack;
	}


	/**
	 * @return string
	 */
	public function getController(): string
	{
		return $this->controller;
	}
}
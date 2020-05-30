<?php

declare(strict_types=1);

namespace Mathematicator\Numbers;


final class NumberFactory
{

	/** @var int */
	private $accuracy;


	/**
	 * @param int $accuracy
	 */
	public function __construct(int $accuracy = 100)
	{
		$this->accuracy = $accuracy;
	}


	/**
	 * @param mixed $number
	 * @return SmartNumber
	 * @throws NumberException
	 */
	public function create($number): SmartNumber
	{
		return new SmartNumber($this->accuracy, (string) $number);
	}
}
<?php

declare(strict_types=1);

namespace Baraja\Localization;


use Nette\MemberAccessException;
use Nette\SmartObject;
use Nette\Utils\ObjectHelpers;

trait TranslateObject
{
	use SmartObject;

	/**
	 * ->getName(): Translation           load property only
	 * ->setName('Jan'): void             set value in current language
	 * ->setName('Honza', 'cs'): void     set value in specific language
	 *
	 * @param string $name
	 * @param string[] $args
	 * @return string|null
	 */
	public function __call(string $name, array $args)
	{
		if (property_exists($this, $name) && ObjectHelpers::hasProperty($class = \get_class($this), $name) === 'event') {
			if (is_iterable($this->$name)) {
				foreach ($this->$name as $handler) {
					$handler(...$args);
				}
			}

			return null;
		}

		if (preg_match('/^(?:get|is)([A-Z].*)$/', $name, $getter)) {
			return $this->{$this->firstLower($getter[1])};
		}

		if (preg_match('/^set([A-Z].*)$/', $name, $setter)) {
			$value = $args[0] ?? null;
			/** @var Translation|null $translation */
			if (($translation = $this->{$propertyName = $this->firstLower($setter[1])}) === null) {
				$translation = new Translation($value, $args[1] ?? null);
			} elseif ($translation->addTranslate($value, $args[1] ?? null) === true) {
				$translation = $translation->regenerate();
			}

			$this->{$propertyName} = $translation;
		} else {
			static $recursion = false;

			if ($recursion === true) {
				$recursion = false;
				throw new MemberAccessException(
					'Call to undefined method ' . \get_class($this) . '::' . $name . '()'
					. (property_exists($this, $name) ? ', did you mean property $' . $name . '?' : '')
				);
			}

			$recursion = true;
			$dynamicCall = $this->$name($args);
			$recursion = false;

			return $dynamicCall;
		}

		return null;
	}


	final public function setPropertyTranslateValue(string $property, ?string $value, ?string $language): void
	{
		$ref = new \ReflectionClass($this);
		$prop = $ref->getProperty($property);
		$prop->setAccessible(true);
		$prop->setValue($this, new Translation($value, $language));
	}


	private function firstLower(string $haystack): string
	{
		return mb_strtolower($haystack[0] ?? '', 'UTF-8') . \substr($haystack, 1);
	}
}

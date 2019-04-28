<?php

	namespace Stoic\Utilities;

	/**
	 * Abstract class to provide basic Enum type
	 * functionality.
	 *
	 * @package Stoic\Utilities
	 * @version 1.0.0
	 */
	abstract class EnumBase implements \JsonSerializable {
		/**
		 * Static internal cache of const lookups.
		 *
		 * @var array
		 */
		protected static $constCache = [];
		

		/**
		 * Internal storage for name.
		 *
		 * @var null|string
		 */
		protected $name = null;
		/**
		 * Internal storage for value.
		 *
		 * @var null|integer
		 */
		protected $value = null;
		/**
		 * Determines whether or not to serialize as the
		 * name of the set value.
		 *
		 * @var boolean
		 */
		protected $serializeAsName = true;


		/**
		 * Static method to return a new Enum object using
		 * the name instead of the value for initialization.
		 *
		 * @param string $string String to use as name.
		 * @param boolean $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 * @return object
		 */
		public static function fromString($string, $serializeAsName = true) {
			$class = get_called_class();

			if ($string === null || empty($string) || !static::validName($string)) {
				return new $class();
			}

			$consts = static::getConstList();
			$ret = new $class(null, $serializeAsName);
			$ret->name = $string;
			$ret->value = $consts['name'][$string];

			return $ret;
		}

		/**
		 * Static method to return the const lookup for the
		 * called class.
		 *
		 * @return array
		 */
		public static function getConstList() {
			$cclass = get_called_class();

			if (array_key_exists($cclass, static::$constCache) === false) {
				$ref = new \ReflectionClass($cclass);
				$cache = [
					'name' => $ref->getConstants(),
					'value' => []
				];

				foreach ($cache['name'] as $name => $value) {
					$cache['value'][$value] = $name;
				}

				static::$constCache[$cclass] = $cache;
			}

			return static::$constCache[$cclass];
		}

		/**
		 * Attempts to instantiate an EnumBase class based on the given
		 * value.  If the value is already the requested class, it is
		 * returned.  If the value is a valid value for the class, a new
		 * instance of the class with the value assigned is returned.  In
		 * all other cases a blank instance of the requested class is
		 * returned.
		 *
		 * @param integer|object $value The value which may or may not be valid as an enum class/value.
		 * @param string $className Fully qualified class name to return.
		 * @throws \InvalidArgumentException
		 * @return EnumBase
		 */
		public static function tryGetEnum($value, $className) {
			if (!is_a($className, EnumBase::class, true)) {
				throw new \InvalidArgumentException("Cannot attempt to retrieve an enum from a class that doesn't extend EnumBase");
			}

			if ($value === null) {
				return new $className();
			}

			if (is_a($value, $className)) {
				return $value;
			}

			if (!$className::validValue($value)) {
				return new $className();
			}

			return new $className($value);
		}

		/**
		 * Static method to validate a name against the Enum
		 * object's possible constants.
		 *
		 * @param string $name String to use as name.
		 * @return boolean
		 */
		public static function validName($name) {
			$consts = static::getConstList();

			return array_key_exists($name, $consts['name']) !== false;
		}

		/**
		 * Static method to validate a value against the Enum
		 * object's possible constants.
		 *
		 * @param integer $value Integer to use as value.
		 * @return boolean
		 */
		public static function validValue($value) {
			$consts = static::getConstList();

			return array_key_exists($value, $consts['value']) !== false;
		}


		/**
		 * Instantiates a new Enum object.
		 *
		 * @param null|integer $value Integer to use as value, defaults to null.
		 * @param mixed $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 */
		public function __construct($value = null, $serializeAsName = true) {
			$this->serializeAsName = $serializeAsName;

			if ($value !== null && static::validValue($value)) {
				$consts = static::getConstList();

				$this->name = $consts['value'][$value];
				$this->value = $value;
			}

			return;
		}

		/**
		 * Serializes Enum object to its string representation.
		 *
		 * @return string
		 */
		public function __toString() {
			if ($this->serializeAsName) {
				return $this->name ?? '';
			}

			return "{$this->value}" ?? '';
		}

		/**
		 * Retrieves the string name of the currently
		 * set value.
		 *
		 * @return null|string
		 */
		public function getName() {
			return $this->name;
		}

		/**
		 * Retrieves the integer representation of the
		 * currently set value.
		 *
		 * @return null|integer
		 */
		public function getValue() {
			return $this->value;
		}

		/**
		 * Determines if the current set value is the same
		 * as the given value.
		 *
		 * @param integer $value Integer to test against current value.
		 * @return boolean
		 */
		public function is($value) {
			if ($this->value === null || $this->value !== $value) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the current value is equal to any of the
		 * supplied values.
		 *
		 * @param integer[] $values Array of integer values to compare against current value.
		 * @return boolean
		 */
		public function isIn(int ...$values) {
			if ($this->value === null) {
				return false;
			}

			foreach (array_values($values) as $val) {
				if ($this->value === $val) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Serializes Enum object to its string representation.
		 *
		 * @return string
		 */
		public function jsonSerialize() {
			return $this->__toString();
		}
	}

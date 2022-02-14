<?php

	namespace Stoic\Utilities;

	use JetBrains\PhpStorm\Pure;

	/**
	 * Abstract class to provide basic Enum type
	 * functionality.
	 *
	 * @package Stoic\Utilities
	 * @version 1.0.1
	 */
	abstract class EnumBase implements \JsonSerializable {
		/**
		 * Internal storage for name.
		 *
		 * @var null|string
		 */
		protected ?string $name = null;
		/**
		 * Internal storage for value.
		 *
		 * @var null|integer
		 */
		protected ?int $value = null;
		/**
		 * Determines whether to serialize as the
		 * name of the set value.
		 *
		 * @var bool
		 */
		protected bool $serializeAsName = true;


		/**
		 * Static internal cache of const lookups.
		 *
		 * @var array[]
		 */
		protected static array $constCache = [];


		/**
		 * Static method to return a new Enum object using
		 * the name instead of the value for initialization.
		 *
		 * @param string $string String to use as name.
		 * @param boolean $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 * @throws \ReflectionException
		 * @return static
		 */
		public static function fromString(string $string, bool $serializeAsName = true) : static {
			$class = get_called_class();

			if (empty($string) || !static::validName($string)) {
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
		 * @throws \ReflectionException
		 * @return array[]
		 */
		public static function getConstList() : array {
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
		 * Attempts to instantiate an EnumBase class based on the given value. If value is the requested class, it is
		 * simply returned. If the value is valid as value, it is used to instantiate a new instance of the class. In all
		 * other cases a blank instance of the class is returned.
		 *
		 * @param int|EnumBase|null $value Value which may be valid for the enum class/value.
		 * @param bool $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 * @throws \ReflectionException
		 * @return static
		 */
		public static function tryGet(null|int|EnumBase $value, bool $serializeAsName = true) : static {
			if ($value === null) {
				return new static();
			}

			$static = new static();

			if (is_a($value, static::class)) {
				return $value;
			}

			if (!self::validValue($value)) {
				return $static;
			}

			$static->setValue($value);

			return $static;
		}

		/**
		 * Attempts to instantiate an EnumBase class based on the given value.  If the value is already the requested
		 * class, it is returned.  If the value is a valid value for the class, a new instance of the class with the value
		 * assigned is returned.  In all other cases a blank instance of the requested class is returned.
		 *
		 * @param integer|static|null $value The value which may or may not be valid as an enum class/value.
		 * @param string $className Fully qualified class name to return.
		 * @param bool $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 * @return EnumBase
		 *@throws \InvalidArgumentException|\ReflectionException
		 */
		public static function tryGetEnum(null|int|EnumBase $value, string $className, bool $serializeAsName = true) : EnumBase {
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
		 * @throws \ReflectionException
		 * @return bool
		 */
		public static function validName(string $name) : bool {
			return array_key_exists($name, static::getConstList()['name']) !== false;
		}

		/**
		 * Static method to validate a value against the Enum
		 * object's possible constants.
		 *
		 * @param integer $value Integer to use as value.
		 * @throws \ReflectionException
		 * @return boolean
		 */
		public static function validValue(int $value) : bool {
			return array_key_exists($value, static::getConstList()['value']) !== false;
		}


		/**
		 * Instantiates a new Enum object.
		 *
		 * @param null|integer $value Integer to use as value, defaults to null.
		 * @param boolean $serializeAsName Causes object to serialize into the name of the set value, defaults to true.
		 * @throws \ReflectionException
		 */
		public function __construct(?int $value = null, bool $serializeAsName = true) {
			$this->serializeAsName = $serializeAsName;
			$this->setValue($value);

			return;
		}

		/**
		 * Serializes Enum object to its string representation.
		 *
		 * @return string
		 */
		public function __toString() : string {
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
		public function getName() : ?string {
			return $this->name;
		}

		/**
		 * Retrieves the integer representation of the
		 * currently set value.
		 *
		 * @return null|integer
		 */
		public function getValue() : ?int {
			return $this->value;
		}

		/**
		 * Determines if the current set value is the same
		 * as the given value.
		 *
		 * @param int $value Integer to test against current value.
		 * @return bool
		 */
		public function is(int $value) : bool {
			if ($this->value === null || $this->value !== $value) {
				return false;
			}

			return true;
		}

		/**
		 * Determines if the current value is equal to any of the
		 * supplied values.
		 *
		 * @param int[] $values Array of integer values to compare against current value.
		 * @return bool
		 */
		public function isIn(int ...$values) : bool {
			if ($this->value === null) {
				return false;
			}

			foreach ($values as $val) {
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
		#[Pure]
		public function jsonSerialize() : string {
			return $this->__toString();
		}

		/**
		 * Internal method to set the value.
		 *
		 * @param int|null $value Integer to use as value, defaults to null,
		 * @throws \ReflectionException
		 * @return void
		 */
		protected function setValue(?int $value = null) : void {
			if ($value === null || !static::validValue($value)) {
				return;
			}

			$this->name  = static::getConstList()['value'][$value];
			$this->value = $value;

			return;
		}
	}

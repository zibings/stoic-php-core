<?php

	namespace Stoic\Utilities;

	abstract class EnumBase implements \JsonSerializable {
		private static $constCache = [];
		
		
		private $name = null;
		private $value = null;
		private $serializeAsName = true;


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

		public static function validName($name) {
			$consts = static::getConstList();
			$lowered = \mb_strtolower($name);

			return array_key_exists($lowered, $consts['name']) !== false;
		}

		public static function validValue($value) {
			$consts = static::getConstList();

			return array_key_exists($value, $consts['value']) !== false;
		}


		public function __construct($value = null, $serializeAsName = true) {
			$this->serializeAsName = $serializeAsName;

			if ($value !== null && static::validValue($value)) {
				$consts = static::getConstList();

				$this->name = $consts['value'][$value];
				$this->value = $value;
			}

			return;
		}

		public function __toString() {
			return $this->name ?? '';
		}

		public function getName() {
			return $this->name;
		}

		public function getValue() {
			return $this->value;
		}

		public function is($value) {
			if ($this->value === null || $this->value !== $value) {
				return false;
			}

			return true;
		}

		public function jsonSerialize() {
			if ($this->serializeAsName) {
				return $this->name ?? '';
			}

			return "{$this->value}" ?? '';
		}
	}

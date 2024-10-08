<?php

	namespace Stoic\Chain;

	/**
	 * Abstract class to provide contract for all nodes used with the chain system.
	 *
	 * @package Stoic\Chain
	 * @version 1.1.0
	 */
	abstract class NodeBase {
		/**
		 * Key that identifies the node.
		 *
		 * @var null|string
		 */
		protected ?string $_key = null;
		/**
		 * Version for the node.
		 *
		 * @var null|string
		 */
		protected ?string $_version = null;


		/**
		 * Serializes object as a string.
		 *
		 * @return string
		 */
		public function __toString() : string {
			return static::class . "{ \"key\": \"{$this->_key}\", \"version\": \"{$this->_version}\" }";
		}

		/**
		 * Returns the node key value.
		 *
		 * @return string
		 */
		public function getKey() : string {
			return $this->_key;
		}

		/**
		 * Returns the node version value.
		 *
		 * @return string
		 */
		public function getVersion() : string {
			return $this->_version;
		}

		/**
		 * Returns whether dispatch is an instance of any provided classes.
		 *
		 * @param DispatchBase $dispatch Dispatch object to check.
		 * @param string ...$classes Classes to check against.
		 * @return bool
		 */
		protected function isDispatchOfType(DispatchBase $dispatch, string ...$classes) : bool {
			foreach ($classes as $class) {
				if ($dispatch instanceof $class) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Returns whether the node is considered valid. This means that there are non-empty values in both the 'key' and
		 * 'version' fields of the node by default.
		 *
		 * @return bool
		 */
		public function isValid() : bool {
			return !empty($this->_key) && !empty($this->_version);
		}

		/**
		 * Abstract method that handles processing of a provided dispatch.
		 * 
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Dispatch object to process.
		 * @return void
		 */
		abstract public function process(mixed $sender, DispatchBase &$dispatch) : void;

		/**
		 * Sets the node key value.
		 *
		 * @param string $key Value to use for node key.
		 * @return NodeBase
		 */
		protected function setKey(string $key) : NodeBase {
			$this->_key = $key;

			return $this;
		}

		/**
		 * Sets the node version value.
		 *
		 * @param string $version Value to use for node version.
		 * @return NodeBase
		 */
		protected function setVersion(string $version) : NodeBase {
			$this->_version = $version;

			return $this;
		}
	}

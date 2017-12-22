<?php

	namespace Stoic\Chain;

	/**
	 * Abstract class to provide contract
	 * for all nodes used with the chain
	 * system.
	 * 
	 * @package Stoic\Chain
	 * @version 1.0.0
	 */
	abstract class NodeBase {
		/**
		 * Key that identifies the node.
		 * 
		 * @var string
		 */
		protected $_key = null;
		/**
		 * Version number for the node.
		 * 
		 * @var string
		 */
		protected $_version = null;


		public function __toString() {
			return static::class . "{ \"key\": \"{$this->_key}\", \"version\": \"{$this->_version}\" }";
		}

		/**
		 * Returns the node key value.
		 * 
		 * @return string
		 */
		public function getKey() {
			return $this->_key;
		}

		/**
		 * Returns the node version value.
		 * 
		 * @return string
		 */
		public function getVersion() {
			return $this->_version;
		}

		/**
		 * Returns whether or not the node is considered
		 * valid.  By default this means that there are
		 * non-empty values in both the 'key' and 'version'
		 * fields of the node.
		 * 
		 * @return boolean
		 */
		public function isValid() {
			return !empty($this->_key) && !empty($this->_version);
		}

		/**
		 * Abstract method that handles processing of a
		 * provided dispatch.
		 * 
		 * @param mixed        $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Dispatch object to process.
		 */
		abstract public function process($sender, DispatchBase &$dispatch);

		/**
		 * Sets the node key value.
		 * 
		 * @param string $key Value to use for node key.
		 * @return NodeBase
		 */
		public function setKey($key) {
			$this->_key = $key;

			return $this;
		}

		/**
		 * Sets the node version value.
		 * 
		 * @param string $version Value to use for node version.
		 * @return NodeBase
		 */
		public function setVersion($version) {
			$this->_version = $version;

			return $this;
		}
	}

<?php

	namespace Stoic\Chain;

	/**
	 * Abstract class to provide contract
	 * for all dispatches used with the
	 * chain system.
	 * 
	 * @package Stoic\Chain
	 * @version 1.0.0
	 */
	abstract class DispatchBase {
		/**
		 * Whether or not the dispatch is 'consumable'.
		 * 
		 * @var boolean
		 */
		protected $_isConsumable = false;
		/**
		 * Whether or not the dispatch should retain state.
		 * 
		 * @var boolean
		 */
		protected $_isStateful = false;
		/**
		 * Whether or not the dispatch has been consumed by a node.
		 * 
		 * @var boolean
		 */
		protected $_isConsumed = false;
		/**
		 * Collection of results from nodes.
		 * 
		 * @var array
		 */
		private $_results = array();
		/**
		 * Whether or not the dispatch is valid for processing.
		 * 
		 * @var boolean
		 */
		protected $_isValid = false;
		/**
		 * Date and time the dispatch was made valid.
		 * 
		 * @var \DateTime
		 */
		private $_calledDateTime;


		public function __toString() {
			return static::class . "{ \"calledDateTime\": \"" . $this->_calledDateTime->format("Y-m-d G:i:s") . "\", " .
				"\"isConsumable\": \"{$this->_isConsumable}\", " .
				"\"isStateful\": \"{$this->_isStateful}\", " .
				"\"isConsumed\": \"{$this->_isConsumed}\" }";
		}

		/**
		 * Marks the dispatch as having been consumed.  If
		 * the dispatch is not consumable or has already
		 * been marked as consumed, returns false.  Otherwise
		 * returns true.
		 * 
		 * @return boolean
		 */
		public function consume() {
			if ($this->_isConsumable && !$this->_isConsumed) {
				$this->_isConsumed = true;

				return true;
			}

			return false;
		}

		/**
		 * Returns time the dispatch was marked valid.
		 * 
		 * @return \DateTime
		 */
		public function getCalledDateTime() {
			return $this->_calledDateTime;
		}

		/**
		 * Returns any results stored in dispatch.  If dispatch
		 * is stateful, this can be multiple results, otherwise
		 * it will be null or a single result.
		 * 
		 * @return array|null
		 */
		public function getResults() {
			if (count($this->_results) < 1) {
				return null;
			}

			return $this->_results;
		}

		/**
		 * Abstract method that handles initialization.  Should
		 * mark dispatch as valid if successful, otherwise
		 * dispatch won't be usable with \ChainHelper objects.
		 * 
		 * @param mixed $input Initialization data for dispatch.
		 */
		abstract public function initialize($input);

		/**
		 * Returns whether or not dispatch can be marked as
		 * consumed.  If toggled and consumed, a \ChainHelper
		 * will refuse to further distribute the dispatch.
		 * 
		 * @return boolean
		 */
		public function isConsumable() {
			return $this->_isConsumable;
		}

		/**
		 * Returns whether or not dispatch has been marked
		 * as consumed.  If consumed, a \ChainHelper will
		 * refuse to further distribute the dispatch.
		 * 
		 * @return boolean
		 */
		public function isConsumed() {
			return $this->_isConsumed;
		}

		/**
		 * Returns whether or not dispatch will hold multiple
		 * results during processing.
		 * 
		 * @return boolean
		 */
		public function isStateful() {
			return $this->_isStateful;
		}

		/**
		 * Returns whether or not dispatch is considered valid
		 * for processing by nodes.
		 * 
		 * @return boolean
		 */
		public function isValid() {
			return $this->_isValid;
		}

		/**
		 * Sets dispatch as consumable.
		 * 
		 * @return DispatchBase
		 */
		public function makeConsumable() {
			$this->_isConsumable = true;

			return $this;
		}

		/**
		 * Sets dispatch as stateful.
		 * 
		 * @return DispatchBase
		 */
		public function makeStateful() {
			$this->_isStateful = true;

			return $this;
		}

		/**
		 * Sets dispatch as valid and records the current
		 * date and time in UTC offset.
		 * 
		 * @return DispatchBase
		 */
		protected function makeValid() {
			$this->_calledDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
			$this->_isValid = true;

			return $this;
		}

		/**
		 * Returns number of results stored in dispatch.
		 * 
		 * @return integer
		 */
		public function numResults() {
			return count($this->_results);
		}

		/**
		 * Sets a result in dispatch.  If dispatch is stateful
		 * result is added to array, otherwise it replaces any
		 * existing results.
		 * 
		 * @param mixed $result Result data to store in dispatch.
		 * @return DispatchBase
		 */
		public function setResult($result) {
			if (!$this->_isStateful) {
				$this->_results = array($result);
			} else {
				$this->_results[] = $result;
			}

			return $this;
		}
	}

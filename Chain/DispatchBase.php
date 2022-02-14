<?php

	namespace Stoic\Chain;

	/**
	 * Abstract class to provide contract
	 * for all dispatches used with the
	 * chain system.
	 * 
	 * @package Stoic\Chain
	 * @version 1.1.0
	 */
	abstract class DispatchBase {
		/**
		 * Whether the dispatch is 'consumable'.
		 * 
		 * @var bool
		 */
		protected bool $_isConsumable = false;
		/**
		 * Whether the dispatch should retain results.
		 * 
		 * @var bool
		 */
		protected bool $_isStateful = false;
		/**
		 * Whether the dispatch has been consumed by a node.
		 * 
		 * @var bool
		 */
		protected bool $_isConsumed = false;
		/**
		 * Collection of results from nodes.
		 *
		 * @var mixed
		 */
		private array $_results = [];
		/**
		 * Whether the dispatch is valid for processing.
		 *
		 * @var bool
		 */
		protected bool $_isValid = false;
		/**
		 * Date and time the dispatch was made valid.
		 * 
		 * @var null|\DateTimeInterface
		 */
		private ?\DateTimeInterface $_calledDateTime = null;


		/**
		 * Serializes the DispatchBase class to a string.
		 *
		 * @return string
		 */
		public function __toString() : string {
			$calledDateTime = ($this->_calledDateTime instanceof \DateTimeInterface) ? $this->_calledDateTime->format("Y-m-d G:i:s") : 'N/A';

			return static::class . "{ \"calledDateTime\": \"" . $calledDateTime . "\", " .
				"\"isConsumable\": \"{$this->_isConsumable}\", " .
				"\"isStateful\": \"{$this->_isStateful}\", " .
				"\"isConsumed\": \"{$this->_isConsumed}\" }";
		}

		/**
		 * Marks the dispatch as having been consumed.  If the dispatch is not consumable or has already been marked as
		 * consumed, returns false.  Otherwise, returns true.
		 * 
		 * @return bool
		 */
		public function consume() : bool {
			if ($this->_isConsumable && !$this->_isConsumed) {
				$this->_isConsumed = true;

				return true;
			}

			return false;
		}

		/**
		 * Returns time the dispatch was marked valid.
		 * 
		 * @return \DateTimeInterface
		 */
		public function getCalledDateTime() : \DateTimeInterface {
			return $this->_calledDateTime;
		}

		/**
		 * Returns any results stored in dispatch.  If dispatch is stateful, this can be multiple results, otherwise it
		 * will be null or a single result.
		 * 
		 * @return mixed
		 */
		public function getResults() : mixed {
			if (count($this->_results) < 1) {
				return null;
			}

			return $this->_results;
		}

		/**
		 * Abstract method that handles initialization.  Should mark dispatch as valid if successful, otherwise dispatch
		 * won't be usable with ChainHelper objects.
		 * 
		 * @param mixed $input Initialization data for dispatch.
		 */
		abstract public function initialize(mixed $input);

		/**
		 * Returns whether dispatch can be marked as consumed.  If toggled and consumed, a ChainHelper will refuse to
		 * further distribute the dispatch.
		 * 
		 * @return bool
		 */
		public function isConsumable() : bool {
			return $this->_isConsumable;
		}

		/**
		 * Returns whether dispatch has been marked as consumed.  If consumed, a ChainHelper will refuse to further
		 * distribute the dispatch.
		 * 
		 * @return bool
		 */
		public function isConsumed() : bool {
			return $this->_isConsumed;
		}

		/**
		 * Returns whether dispatch will hold multiple results during processing.
		 * 
		 * @return bool
		 */
		public function isStateful() : bool {
			return $this->_isStateful;
		}

		/**
		 * Returns whether dispatch is considered valid for processing by nodes.
		 * 
		 * @return bool
		 */
		public function isValid() : bool {
			return $this->_isValid;
		}

		/**
		 * Sets dispatch as consumable.
		 * 
		 * @return DispatchBase
		 */
		protected function makeConsumable() : DispatchBase {
			$this->_isConsumable = true;

			return $this;
		}

		/**
		 * Sets dispatch as stateful.
		 * 
		 * @return DispatchBase
		 */
		protected function makeStateful() : DispatchBase {
			$this->_isStateful = true;

			return $this;
		}

		/**
		 * Sets dispatch as valid and records the current date and time in UTC offset.
		 *
		 * @throws \Exception
		 * @return DispatchBase
		 */
		protected function makeValid() : DispatchBase {
			$this->_calledDateTime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
			$this->_isValid = true;

			return $this;
		}

		/**
		 * Returns number of results stored in dispatch.
		 * 
		 * @return int
		 */
		public function numResults() : int {
			return count($this->_results);
		}

		/**
		 * Sets a result in dispatch.  If dispatch is stateful result is added to array, otherwise it replaces any existing
		 * results.
		 * 
		 * @param mixed $result Result data to store in dispatch.
		 * @return DispatchBase
		 */
		public function setResult(mixed $result) : DispatchBase {
			if (!$this->_isStateful) {
				$this->_results = [$result];
			} else {
				$this->_results[] = $result;
			}

			return $this;
		}
	}

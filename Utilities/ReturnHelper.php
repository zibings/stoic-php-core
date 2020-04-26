<?php

	namespace Stoic\Utilities;

	/**
	 * Class to provide more data for
	 * method/function returns.
	 * 
	 * @package Stoic\Utilities
	 * @version 1.0.0
	 */
	class ReturnHelper {
		/**
		 * Array of messages in this return.
		 * 
		 * @var string[]
		 */
		protected $_messages;
		/**
		 * Array of results in this return.
		 * 
		 * @var mixed[]
		 */
		protected $_results;
		/**
		 * Current status of this return.
		 * 
		 * @var integer
		 */
		protected $_status;


		const STATUS_BAD = 0;
		const STATUS_GOOD = 1;


		/**
		 * Instantiates a new ReturnHelper class. Default
		 * status is STATUS_BAD.
		 */
		public function __construct() {
			$this->_messages = [];
			$this->_results = [];
			$this->_status = self::STATUS_BAD;

			return;
		}

		/**
		 * Adds a message onto the internal collection.
		 * 
		 * @param string $message String value of message to add to collection.
		 */
		public function addMessage(string $message) : void {
			$this->_messages[] = $message;

			return;
		}

		/**
		 * Adds a group of messages onto the internal
		 * collection.
		 * 
		 * @param string[] $messages Array of strings to add to collection.
		 * @throws \InvalidArgumentException Thrown if null or empty array provided.
		 */
		public function addMessages(array $messages) : void {
			if ($messages === null || count($messages) < 1) {
				throw new \InvalidArgumentException("Messages array to ReturnHelper::addMessages() must be array with elements");
			}

			foreach (array_values($messages) as $msg) {
				$this->_messages[] = $msg;
			}

			return;
		}

		/**
		 * Adds a result onto the internal collection.
		 * 
		 * @param mixed $result Result value to add to collection.
		 */
		public function addResult($result) : void {
			$this->_results[] = $result;

			return;
		}

		/**
		 * Adds a group of results onto the internal
		 * collection.
		 * 
		 * @param mixed[] $results Array of results to add to collection.
		 * @throws \InvalidArgumentException Thrown if null or empty array provided.
		 */
		public function addResults(array $results) : void {
			if ($results === null || count($results) < 1) {
				throw new \InvalidArgumentException("Results array to ReturnHelper::addResults() must be array with elements");
			}

			foreach (array_values($results) as $res) {
				$this->_results[] = $res;
			}

			return;
		}

		/**
		 * Returns TRUE if the current internal status
		 * is set to STATUS_BAD.
		 * 
		 * @return boolean
		 */
		public function isBad() : bool {
			return $this->_status === self::STATUS_BAD;
		}

		/**
		 * Returns TRUE if the current internal status
		 * is set to STATUS_GOOD.
		 * 
		 * @return boolean
		 */
		public function isGood() : bool {
			return $this->_status === self::STATUS_GOOD;
		}

		/**
		 * Returns the internal collection of messages.
		 * 
		 * @return string[]
		 */
		public function getMessages() {
			return $this->_messages;
		}

		/**
		 * Returns the internal collection of results.
		 * 
		 * @return mixed[]
		 */
		public function getResults() {
			return $this->_results;
		}

		/**
		 * Returns TRUE if there are messages stored in
		 * the internal collection.
		 * 
		 * @return boolean
		 */
		public function hasMessages() : bool {
			return count($this->_messages) > 0;
		}

		/**
		 * Returns TRUE if there are results stored in
		 * the internal collection.
		 * 
		 * @return boolean
		 */
		public function hasResults() : bool {
			return count($this->_results) > 0;
		}

		/**
		 * Sets the internal status as STATUS_BAD.
		 */
		public function makeBad() : void {
			$this->_status = self::STATUS_BAD;

			return;
		}

		/**
		 * Sets the internal status as STATUS_GOOD.
		 */
		public function makeGood() : void {
			$this->_status = self::STATUS_GOOD;

			return;
		}
	}

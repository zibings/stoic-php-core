<?php

	namespace Stoic\Utilities;

	/**
	 * Class to provide more data for method/function returns.
	 *
	 * @package Stoic\Utilities
	 * @version 1.1.0
	 */
	class ReturnHelper {
		/**
		 * Array of messages in this return.
		 *
		 * @var string[]
		 */
		protected array $_messages;
		/**
		 * Array of results in this return.
		 *
		 * @var array[]
		 */
		protected array $_results;
		/**
		 * Current status of this return.
		 *
		 * @var int
		 */
		protected int $_status;


		const int STATUS_BAD  = 0;
		const int STATUS_GOOD = 1;


		/**
		 * Instantiates a new ReturnHelper class. Default status is STATUS_BAD.
		 */
		public function __construct() {
			$this->_messages = [];
			$this->_results  = [];
			$this->_status   = self::STATUS_BAD;

			return;
		}

		/**
		 * Adds a message onto the internal collection.
		 *
		 * @param string $message String value of message to add to collection.
		 * @param int $weight Optional integer value to use as message weight.
		 * @return void
		 */
		public function addMessage(string $message, int $weight = 1) : void {
			$this->_messages[] = [
				'message' => $message,
				'weight'  => $weight
			];

			return;
		}

		/**
		 * Adds a group of messages onto the internal collection.  Messages can be either strings or message pairs:
		 *
		 * [
		 *   'This is the message',  // the string message
		 *   1                       // the weight of the message
		 * ]
		 *
		 * The $defaultWeight is only used for pure string messages.
		 *
		 * @param string[]|array $messages Array of strings or messages to add to collection.
		 * @param int $defaultWeight Optional integer value to use as default message weight.
		 * @throws \InvalidArgumentException
		 * @return void
		 */
		public function addMessages(array $messages, int $defaultWeight = 1) : void {
			if (count($messages) < 1) {
				throw new \InvalidArgumentException("Messages array to ReturnHelper::addMessages() must be array with elements");
			}

			foreach ($messages as $msg) {
				if (is_array($msg)) {
					if (count($msg) != 2) {
						continue;
					}

					if (!is_int($msg[1])) {
						$this->addMessage($msg[0], $defaultWeight);
					} else {
						$this->addMessage($msg[0], $msg[1]);
					}

					continue;
				}

				if (!is_string($msg)) {
					continue;
				}

				$this->addMessage($msg, $defaultWeight);
			}

			return;
		}

		/**
		 * Adds a result onto the internal collection.
		 *
		 * @param mixed $result Result value to add to collection.
		 * @return void
		 */
		public function addResult(mixed $result) : void {
			$this->_results[] = $result;

			return;
		}

		/**
		 * Adds a group of results onto the internal collection.
		 *
		 * @param array[] $results Array of results to add to collection.
		 * @throws \InvalidArgumentException
		 * @return void
		 */
		public function addResults(array $results) : void {
			if (count($results) < 1) {
				throw new \InvalidArgumentException("Results array to ReturnHelper::addResults() must be array with elements");
			}

			foreach ($results as $res) {
				$this->_results[] = $res;
			}

			return;
		}

		/**
		 * Internal method to flatten a message pair into it's string.
		 *
		 * @param array $value Message pair value from message stack.
		 * @return string
		 */
		protected function flattenMessage(array $value) : string {
			return $value['message'];
		}

		/**
		 * Returns TRUE if the current internal status is set to STATUS_BAD.
		 *
		 * @return bool
		 */
		public function isBad() : bool {
			return $this->_status === self::STATUS_BAD;
		}

		/**
		 * Returns TRUE if the current internal status is set to STATUS_GOOD.
		 *
		 * @return bool
		 */
		public function isGood() : bool {
			return $this->_status === self::STATUS_GOOD;
		}

		/**
		 * Returns the internal collection of messages as strings.
		 *
		 * @param bool $reversed Optionally return messages in reverse order they were added.
		 * @return string[]
		 */
		public function getMessages(bool $reversed = false) : array {
			if ($reversed) {
				return array_reverse(array_map($this->flattenMessage(...), $this->_messages));
			}

			return array_map($this->flattenMessage(...), $this->_messages);
		}

		/**
		 * Returns the internal collection of messages as strings, ordered by their weight.
		 *
		 * @param bool $reversed Optionally returns messages in reverse weighted order.
		 * @return array
		 */
		public function getMessagesWeighted(bool $reversed = false) : array {
			$cmpFunc = function ($a, $b) {
				if ($a['weight'] == $b['weight']) {
					return 0;
				}

				return $a['weight'] < $b['weight'] ? -1 : 1;
			};

			$messagesCopy = [...$this->_messages];
			usort($messagesCopy, $cmpFunc);

			if ($reversed) {
				return array_reverse(array_map($this->flattenMessage(...), $messagesCopy));
			}

			return array_map($this->flattenMessage(...), $messagesCopy);
		}

		/**
		 * Returns the internal collection of results.
		 *
		 * @return array[]
		 */
		public function getResults() : array {
			return $this->_results;
		}

		/**
		 * Returns TRUE if there are messages stored in the internal collection.
		 *
		 * @return bool
		 */
		public function hasMessages() : bool {
			return count($this->_messages) > 0;
		}

		/**
		 * Returns TRUE if there are results stored in the internal collection.
		 *
		 * @return bool
		 */
		public function hasResults() : bool {
			return count($this->_results) > 0;
		}

		/**
		 * Sets the internal status as STATUS_BAD.
		 *
		 * @return void
		 */
		public function makeBad() : void {
			$this->_status = self::STATUS_BAD;

			return;
		}

		/**
		 * Sets the internal status as STATUS_GOOD.
		 *
		 * @return void
		 */
		public function makeGood() : void {
			$this->_status = self::STATUS_GOOD;

			return;
		}
	}

<?php

	namespace Stoic\Log;

	use Stoic\Chain\DispatchBase;

	/**
	 * Describes a collection of messages that
	 * will be passed to log appenders.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.1
	 */
	class MessageDispatch extends DispatchBase {
		/**
		 * Collection of Message objects.
		 * 
		 * @var Message[]
		 */
		public $messages = [];


		/**
		 * Initializes the dispatch message collection.
		 * 
		 * @param Message[] $messages Collection of Message objects to handle.
		 */
		public function initialize($input) : void {
			if (!is_array($input) && (!is_object($input) || !($input instanceof Message))) {
				return;
			}

			if (is_array($input)) {
				if (count($input) < 1) {
					return;
				}

				foreach (array_values($input) as $msg) {
					if ($msg instanceof Message) {
						$this->messages[] = $msg;
					}
				}
			} else {
				$this->messages[] = $input;
			}

			$this->makeValid();

			return;
		}
	}

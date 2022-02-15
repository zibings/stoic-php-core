<?php

	namespace Stoic\Log;

	use Stoic\Chain\DispatchBase;

	/**
	 * Describes a collection of messages that will be passed to log appenders.
	 *
	 * @package Stoic\Log
	 * @version 1.1.0
	 */
	class MessageDispatch extends DispatchBase {
		/**
		 * Collection of Message objects.
		 *
		 * @var Message[]
		 */
		public array $messages = [];


		/**
		 * Initializes the dispatch message collection.
		 *
		 * @param Message|Message[] $input Collection of Message objects to handle.
		 * @throws \Exception
		 * @return void
		 */
		public function initialize($input) : void {
			if (!is_array($input) && (!is_object($input) || !($input instanceof Message))) {
				return;
			}

			if (is_array($input)) {
				if (count($input) < 1) {
					return;
				}

				foreach ($input as $msg) {
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

<?php

	namespace Stoic\Log;

	use Stoic\Chain\DispatchBase;

	/**
	 * Null-sink/noop log appender.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.1
	 */
	class NullAppender extends AppenderBase {
		/**
		 * Instantiates a new NullAppender object.
		 *
		 * @codeCoverageIgnore
		 */
		public function __construct() {
			$this->setKey('NullAppender');
			$this->setVersion('1.0.0');

			return;
		}

		/**
		 * Append method which simply consumes the collection
		 * of log messages and returns without caring.
		 * 
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Collection of Message objects to handle.
		 * @codeCoverageIgnore
		 */
		public function process($sender, DispatchBase &$dispatch) : void {
			return;
		}
	}

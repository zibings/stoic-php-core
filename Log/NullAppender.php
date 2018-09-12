<?php

	namespace Stoic\Log;

	use Stoic\Chain\DispatchBase;

	/**
	 * Null-sink/noop log appender.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.0
	 */
	class NullAppender extends AppenderBase {
		/**
		 * Append method which simply consumes the collection
		 * of log messages and returns without caring.
		 * 
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Collection of Message objects to handle.
		 */
		public function process($sender, DispatchBase &$dispatch) {
			return;
		}
	}

<?php

	namespace Stoic\Log;

	use Psr\Log\AbstractLogger;

	class Logger extends AbstractLogger {
		/**
		 * Holds all assigned appenders.
		 * 
		 * @var LogAppenderBase[]
		 */
		private $appenders = array();
		/**
		 * Holds all messages for appenders.
		 * 
		 * @var LogMessage[]
		 */
		private $messages = array();


		/**
		 * Instantiates a new Logger object.
		 * 
		 * Creates a new Logger object.  Optionally
		 * accepts an array of LogAppenderBase objects
		 * to assign to appender stack.  Any objects in
		 * array that don't implement LogAppenderBase
		 * are simply ignored.
		 * 
		 * @param null|LogAppenderBase[] $appenders Optional collection of LogAppenderBase objects to assign.
		 */
		public function __construct(array $appenders = null) {
			if ($appenders !== null && count($appenders) > 0) {
				foreach (array_values($appenders) as $appdr) {
					if ($appdr instanceof LogAppenderBase) {
						$this->appenders[] = $appdr;
					}
				}
			}

			return;
		}

		/**
		 * Logs with an arbitrary level.
		 * 
		 * Generates a Stoic\LogMessage object for
		 * the provided message
		 *
		 * @param string $level
		 * @param string $message
		 * @param array $context
		 */
		public function log($level, $message, array $context = array()) {

		}

		protected function interpolate($message, array $context) {
			
		}
	}

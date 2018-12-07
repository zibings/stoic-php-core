<?php

	namespace Stoic\Log;

	use Psr\Log\AbstractLogger;
	use Psr\Log\LogLevel;
	use Stoic\Chain\ChainHelper;

	/**
	 * PSR-3 compliant logging class which accepts
	 * multiple appenders for output/handling.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.0
	 */
	class Logger extends AbstractLogger {
		/**
		 * Holds all assigned appenders.
		 * 
		 * @var ChainHelper
		 */
		private $appenders = null;
		/**
		 * Holds all messages for appenders.
		 * 
		 * @var Message[]
		 */
		private $messages = array();
		/**
		 * The minimum log level to push to appennders.
		 * 
		 * @var string
		 */
		private $minLevel = LogLevel::DEBUG;
		/**
		 * Collection of log levels numerically indexed
		 * to allow for minimum level comparison.
		 * 
		 * @var string[]
		 */
		protected static $levels = array(
			LogLevel::DEBUG,
			LogLevel::INFO,
			LogLevel::NOTICE,
			LogLevel::WARNING,
			LogLevel::ERROR,
			LogLevel::CRITICAL,
			LogLevel::ALERT,
			LogLevel::EMERGENCY
		);


		/**
		 * Instantiates a new Logger object.
		 * 
		 * Creates a new Logger object.  Optionally
		 * accepts an array of LogAppenderBase objects
		 * to assign to appender stack.  Any objects in
		 * array that don't implement LogAppenderBase
		 * are simply ignored.
		 * 
		 * @param null|string $minimumLevel Optional minimum log level for output; Default is LogLevel::DEBUG
		 * @param null|AppenderBase[] $appenders Optional collection of LogAppenderBase objects to assign.
		 */
		public function __construct($minimumLevel = null, array $appenders = null) {
			$this->appenders = new ChainHelper();

			if ($minimumLevel !== null) {
				if (in_array($minimumLevel, static::$levels) === false) {
					throw new \InvalidArgumentException("Invalid log level supplied to Logger constructor");
				}

				$this->minLevel = $minimumLevel;
			}

			if ($appenders !== null && count($appenders) > 0) {
				foreach (array_values($appenders) as $appdr) {
					if ($appdr instanceof AppenderBase) {
						$this->appenders->linkNode($appdr);
					}
				}
			}

			return;
		}

		/**
		 * Adds a new appender to the appender stack.
		 * 
		 * @param AppenderBase $appender Appender which extends the AppenderBase abstract class.
		 * @throws \Psr\Log\InvalidArgumentException Thrown if invalid appender argument provided.
		 */
		public function addAppender(AppenderBase $appender) {
			$this->appenders->linkNode($appender);

			return;
		}

		/**
		 * Interpolates context values into message placeholders.
		 * 
		 * @param string $message String value of log message w/ potential placeholders.
		 * @param array $context Array of context values to interpolate with placeholers.
		 * @return string
		 */
		protected function interpolate($message, array $context) {
			if (strpos($message, '{') === false || strpos($message, '}') === false) {
				return $message;
			}

			$replacements = array();

			foreach ($context as $key => $val) {
				$rkey = "{{$key}}";

				if ($val === null) {
					$replacements[$rkey] = 'null';
				} else if (is_scalar($val) || (is_object($val) && method_exists($val, '__toString')
						&& !($val instanceof \Exception) && !($val instanceof \DateTimeInterface))) {
					$replacements[$rkey] = $val;
				} else if (is_object($val)) {
					if ($val instanceof \Exception) {
						$replacements[$rkey] = "[exception " . get_class($val) . "]\n\tMessage: {$val->getMessage()}\n\tStack Trace: {$val->getTraceAsString()}";
					} else if ($val instanceof \DateTimeInterface) {
						$replacements[$rkey] = $val->format(\DateTime::RFC3339);
					} else {
						$replacements[$rkey] = "[object " . get_class($val) . "]";
					}
				} else {
					$replacements[$rkey] = "[" . gettype($val) . "]";
				}
			}

			return str_replace(array_keys($replacements), array_values($replacements), $message);
		}

		/**
		 * Logs with an arbitrary level.
		 * 
		 * Generates a Stoic\LogMessage object for
		 * the provided message
		 *
		 * @param string $level String value of log level for message.
		 * @param string $message String value of log message.
		 * @param array $context Optional context array for replacing placeholders in message string.
		 */
		public function log($level, $message, array $context = array()) {
			$this->messages[] = new Message($level, $this->interpolate($message, $context));

			return;
		}

		/**
		 * Determines if the given log level meets the
		 * configured minimum level.
		 * 
		 * @param string $level String value of level to check against minimum.
		 * @return boolean
		 */
		protected function meetsMinimumLevel($level) {
			return array_search($level, self::$levels) >= array_search($this->minLevel, self::$levels);
		}

		/**
		 * Generates the level-filtered collection of
		 * messages to output and traverses them through
		 * the appender stack.
		 */
		public function output() {
			$messages = array();

			foreach (array_values($this->messages) as $message) {
				if ($this->meetsMinimumLevel($message->level)) {
					$messages[] = $message;
				}
			}

			if (count($messages) < 1) {
				return;
			}

			$disp = new MessageDispatch();
			$disp->initialize($messages);
			$this->messages = array();

			$this->appenders->traverse($disp, $this);

			return;
		}
	}

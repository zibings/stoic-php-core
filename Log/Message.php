<?php

	namespace Stoic\Log;

	use Psr\Log\LogLevel;
	use Psr\Log\InvalidArgumentException;

	/**
	 * Represents a log message.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.0
	 */
	class Message implements \JsonSerializable {
		/**
		 * String value of message level.
		 * 
		 * @var string
		 */
		public $level;
		/**
		 * String value of log message.
		 * 
		 * @var string
		 */
		public $message;
		/**
		 * Immutable timestamp for log
		 * message creation time.
		 * 
		 * @var \DateTimeInterface
		 */
		private $timestamp;
		/**
		 * Static collection of log levels
		 * to speed checking of level validity.
		 * 
		 * @var array
		 */
		private static $validLevels = array(
			LogLevel::DEBUG => true,
			LogLevel::INFO => true,
			LogLevel::NOTICE => true,
			LogLevel::WARNING => true,
			LogLevel::ERROR => true,
			LogLevel::CRITICAL => true,
			LogLevel::ALERT => true,
			LogLevel::EMERGENCY => true
		);


		/**
		 * Instantiates a new Message object with
		 * given level and message.
		 * 
		 * @param string $level String value of message level.
		 * @param string $message String value of log message.
		 * @throws \InvalidArgumentException Thrown if invalid log level provided.
		 */
		public function __construct(string $level, string $message) {
			if (array_key_exists($level, self::$validLevels) === false) {
				throw new \InvalidArgumentException("Invalid log level provided to Stoic\Log\LogMessage: {$level}");
			}

			$this->level = $level;
			$this->message = $message;

			$timeParts = gettimeofday();
			$this->timestamp = new \DateTimeImmutable(date('Y-m-d G:i:s.', $timeParts['sec']) . $timeParts['usec'], new \DateTimeZone('UTC'));

			return;
		}

		/**
		 * Returns the immutable timestamp marking
		 * message creation.
		 * 
		 * @return \DateTimeInterface
		 */
		public function getTimestamp() : \DateTimeInterface {
			return $this->timestamp;
		}

		/**
		 * Produces an array containing all data
		 * within the Message object.
		 * 
		 * @return string[]
		 */
		public function __toArray() {
			return array(
				'level' => $this->level,
				'message' => $this->message,
				'timestamp' => $this->timestamp->format('Y-m-d G:i:s.u')
			);
		}

		/**
		 * Produces a JSON object containing all
		 * data within the Message object.
		 * 
		 * @return string
		 */
		public function __toJson() : string {
			return json_encode($this);
		}

		/**
		 * Returns an array of the message data ready to run through
		 * json_encode().
		 *
		 * @return string[]
		 */
		public function jsonSerialize() {
			return [
				'level' => strtoupper($this->level),
				'message' => $this->message,
				'timestamp' => $this->timestamp->format('Y-m-d G:i:s.u')
			];
		}

		/**
		 * Produces a basic string containing all
		 * data within the Message object.
		 * 
		 * @return string
		 */
		public function __toString() : string {
			return sprintf("%s %' -9s %s",
				$this->timestamp->format('Y-m-d G:i:s.u'),
				strtoupper($this->level),
				$this->message
			);
		}
	}

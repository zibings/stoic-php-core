<?php

	namespace Stoic\Log;

	use Psr\Log\LogLevel;
	use Psr\Log\InvalidArgumentException;

	class LogMessage {
		public $level;
		public $message;
		private $timestamp;
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


		public function __construct($level, $message) {
			if (array_key_exists($level, self::$validLevels) === false) {
				throw new InvalidArgumentException("Invalid log level provided to Stoic\Log\LogMessage: {$level}");
			}

			$this->level = $level;
			$this->message = $message;

			$timeParts = explode('.', microtime(true));
			$this->timestamp = new \DateTimeImmutable(date('Y-m-d G:i:s.', $timeParts[0]) . $timeParts[1], new \DateTimeZone('UTC'));

			return;
		}

		public function getTimestamp() {
			return $this->timestamp;
		}

		public function __toArray() {
			return array(
				'level' => $this->level,
				'message' => $this->message,
				'timestamp' => $this->timestamp->format('Y-m-d G:i:s.u')
			);
		}

		public function __toJson() {
			return sprintf("{ \"level\": \"%s\", \"message\": \"%s\", \"timestamp\": \"%s\" }",
				strtoupper($this->level),
				$this->message,
				$this->timestamp->format('Y-m-d G:i:s.u')
			);
		}

		public function __toString() {
			return sprintf("%s %' -9s %s",
				$this->timestamp->format('Y-m-d G:i:s.u'),
				strtoupper($this->level),
				$this->message
			);
		}
	}

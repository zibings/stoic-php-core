<?php

	namespace Stoic\Log;

	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;

	/**
	 * Describes a Stoic Log Appender which can
	 * do many-splendored things with log messages.
	 * 
	 * @package Stoic\Log
	 * @version 1.0.0
	 */
	abstract class AppenderBase extends NodeBase {
		/**
		 * Receives one or more Message objects to
		 * be handled by the appender.
		 * 
		 * @param mixed $sender Sender data, optional and thus can be 'null'.
		 * @param DispatchBase $dispatch Collection of Message objects to handle.
		 */
		abstract public function process($sender, DispatchBase &$dispatch);
	}

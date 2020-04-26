<?php

	namespace Stoic\Chain;

	/**
	 * Class to maintain groups (chains) of nodes
	 * and send events to them.
	 * 
	 * @package Stoic\Chain
	 * @version 1.0.1
	 */
	class ChainHelper {
		/**
		 * Group of nodes (one or more).
		 * 
		 * @var array
		 */
		protected $_nodes = [];
		/**
		 * Whether or not instance is an event-chain.
		 * 
		 * @var boolean
		 */
		protected $_isEvent = false;
		/**
		 * Whether or not instance should send debug messages.
		 * 
		 * @var boolean
		 */
		protected $_doDebug = false;
		/**
		 * Optional callback that receives debug messages (if enabled).
		 * 
		 * @var \callable
		 */
		protected $_logger = null;


		/**
		 * Creates new instance of ChainHelper class.  If set
		 * as an event-chain, only one node may be linked to
		 * chain at any given time.
		 * 
		 * @param boolean $isEvent Toggle for event-chain.
		 * @param boolean $doDebug Toggle for sending debug messages.
		 */
		public function __construct(bool $isEvent = false, bool $doDebug = false) {
			$this->_isEvent = $isEvent;
			$this->toggleDebug($doDebug);

			return;
		}

		/**
		 * Toggles the use of debug messages by this instance.
		 * 
		 * @param boolean $doDebug Toggle for sending debug messages.
		 * @return ChainHelper
		 */
		public function toggleDebug(bool $doDebug) : ChainHelper {
			$this->_doDebug = ($doDebug) ? true : false;

			return $this;
		}

		/**
		 * Returns the full list of nodes linked to the chain.
		 * 
		 * @return array
		 */
		public function getNodeList() {
			$ret = array();

			foreach (array_values($this->_nodes) as $node) {
				$ret[] = array(
					'key' => $node->getKey(),
					'version' => $node->getVersion()
				);
			}

			return $ret;
		}

		/**
		 * Attaches the given callback to the chain to
		 * receive debug messages, if enabled.  Callbacks
		 * should accept a single string argument.
		 * 
		 * @param callable $callback Callable method/function that receives messages.
		 * @return void
		 */
		public function hookLogger(callable $callback) : void {
			$this->_logger = $callback;

			return;
		}

		/**
		 * Returns whether or not chain is setup as an
		 * event-chain.
		 * 
		 * @return boolean
		 */
		public function isEvent() : bool {
			return $this->_isEvent;
		}

		/**
		 * Registers a NodeBase object with the chain.  If
		 * chain is an event-chain, this will overwrite any
		 * existing node.  If node is invalid, link will fail.
		 * 
		 * @param NodeBase $node NodeBase object to register with chain.
		 * @return ChainHelper
		 */
		public function linkNode(NodeBase $node) : ChainHelper {
			if (!$node->isValid()) {
				if ($this->_doDebug) {
					$this->log("Attempted to add invalid node: " . $node);
				}

				return $this;
			}

			if ($this->_isEvent) {
				if ($this->_doDebug) {
					$this->log("Setting event node: " . $node);
				}

				$this->_nodes = array($node);
			} else {
				if ($this->_doDebug) {
					$this->log("Linking new node: " . $node);
				}

				$this->_nodes[] = $node;
			}

			return $this;
		}

		/**
		 * Triggers distribution of given dispatch to all
		 * linked nodes in chain.  Will return false if no
		 * nodes are linked, the dispatch is invalid, or
		 * the dispatch is consumable and has already been
		 * consumed.
		 * 
		 * @param DispatchBase $dispatch DispatchBase object to distribute to linked nodes.
		 * @param mixed $sender Optional sender data to pass to linked nodes.
		 * @return boolean
		 */
		public function traverse(DispatchBase &$dispatch, $sender = null) : bool {
			if (count($this->_nodes) < 1) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with no nodes");
				}

				return false;
			}

			if (!$dispatch->isValid()) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with invalid dispatch: " . $dispatch);
				}

				return false;
			}

			if ($dispatch->isConsumable() && $dispatch->isConsumed()) {
				if ($this->_doDebug) {
					$this->log("Attempted to traverse chain with consumed dispatch: " . $dispatch);
				}

				return false;
			}

			if ($sender === null) {
				$sender = $this;
			}

			$isConsumable = $dispatch->isConsumable();

			if ($this->_isEvent) {
				if ($this->_doDebug) {
					$this->log("Sending dispatch (" . $dispatch . ") to event node: " . $this->_nodes[0]);
				}

				$this->_nodes[0]->process($sender, $dispatch);
			} else {
				$len = count($this->_nodes);

				for ($i = 0; $i < $len; ++$i) {
					if ($this->_doDebug) {
						$this->log("Sending dispatch (" . $dispatch . ") to node: " . $this->_nodes[$i]);
					}

					$this->_nodes[$i]->process($sender, $dispatch);

					if ($isConsumable && $dispatch->isConsumed()) {
						if ($this->_doDebug) {
							$this->log("Dispatch (" . $dispatch . ") consumed by node: " . $this->_nodes[$i]);
						}

						break;
					}
				}
			}

			return true;
		}

		/**
		 * Conditionally sends debug message to registered
		 * callback.
		 * 
		 * @param string $message Message to send to callback.
		 * @return void
		 */
		protected function log(string $message) : void {
			if ($this->_logger !== null) {
				call_user_func($this->_logger, $message);
			}

			return;
		}
	}

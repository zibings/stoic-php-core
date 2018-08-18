<?php

	namespace Stoic\Chain\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;

	class LogCache {
		public $_messages = array();

		public function receiveLog($message) {
			$this->_messages[] = $message;
		}
	}

	class TestDispatch extends DispatchBase {
		public function initialize($input) {
			$this->makeValid();

			return;
		}
	}

	class TestConsumableDispatch extends DispatchBase {
		public function initialize($input) {
			$this->makeConsumable();
			$this->makeValid();

			return;
		}
	}

	class NonConsumeTestNode extends NodeBase {
		public function __construct() {
			$this->setKey("NonConsumeTestNode")->setVersion("1.0.0");

			return;
		}

		public function process($sender, DispatchBase &$dispatch) {
			return;
		}
	}

	class ConsumeTestNode extends NodeBase {
		public function __construct() {
			$this->setKey("ConsumeTestNode")->setVersion("1.0.0");

			return;
		}

		public function process($sender, DispatchBase &$dispatch) {
			$dispatch->consume();

			return;
		}
	}

	class LoggingTest extends TestCase {
		public function test_logMessageCount() {
			$lg_nonevent = new LogCache();
			$lg_event = new LogCache();
			$lg_norm = new LogCache();
			$lg_consume = new LogCache();
			
			$ch_nonevent = new ChainHelper(false, true);
			$ch_event = new ChainHelper(true, true);
			$ch_norm = new ChainHelper();
			$ch_consume = new ChainHelper(false, true);

			self::assertTrue($ch_nonevent->hookLogger(array($lg_nonevent, "receiveLog")));
			self::assertTrue($ch_event->hookLogger(array($lg_event, "receiveLog")));
			self::assertTrue($ch_norm->hookLogger(array($lg_norm, "receiveLog")));
			self::assertTrue($ch_consume->hookLogger(array($lg_consume, "receiveLog")));

			$ch_nonevent->linkNode(new NonConsumeTestNode());
			$ch_nonevent->linkNode(new ConsumeTestNode());

			$ch_event->linkNode(new NonConsumeTestNode());
			$ch_event->linkNode(new ConsumeTestNode());

			$ch_norm->linkNode(new NonConsumeTestNode());
			$ch_norm->linkNode(new ConsumeTestNode());

			$ch_consume->linkNode(new ConsumeTestNode());
			$ch_consume->linkNode(new NonConsumeTestNode());

			$disp = new TestDispatch();
			$disp->initialize([]);

			$cdisp = new TestConsumableDispatch();
			$cdisp->initialize([]);

			$ch_nonevent->traverse($disp);
			$ch_event->traverse($disp);
			$ch_norm->traverse($disp);
			$ch_consume->traverse($cdisp);

			self::assertEquals(4, count($lg_nonevent->_messages));
			self::assertEquals(3, count($lg_event->_messages));
			self::assertEquals(0, count($lg_norm->_messages));
			self::assertEquals(4, count($lg_consume->_messages));

			return;
		}
	}

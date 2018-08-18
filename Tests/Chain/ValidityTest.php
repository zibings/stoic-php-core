<?php

	namespace Stoic\Chain\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;

	class InvalidDispatch extends DispatchBase {
		public function initialize($input) {
			$this->_isValid = false;
		}
	}

	class ValidDispatch extends DispatchBase {
		public function initialize($input) {
			$this->makeValid();
		}
	}

	class ValidNode extends NodeBase {
		public function __construct() {
			$this->_key     = 'ValidNode';
			$this->_version = '1.0.0';
		}

		public function process($sender, DispatchBase &$Dispatch) {
		}
	}

	class InvalidNode extends NodeBase {
		public function process($sender, DispatchBase &$Dispatch) {
		}
	}

	class DispatchTest extends TestCase {
		public function test_invalidNodeGetsRemoved() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new InvalidNode())->linkNode(new ValidNode());

			self::assertCount(1, $chainHelper->getNodeList());

			return;
		}

		public function test_invalidDispatchFails() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new ValidNode());

			$dispatch      = new InvalidDispatch();
			$shouldBeFalse = $chainHelper->traverse($dispatch);

			self::assertFalse($shouldBeFalse);

			return;
		}

		public function test_validDispatchAndValidNodePasses() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new ValidNode());

			$dispatch = new ValidDispatch();
			$dispatch->initialize(null);

			$shouldBeTrue = $chainHelper->traverse($dispatch);

			self::assertTrue($shouldBeTrue);

			return;
		}
	}

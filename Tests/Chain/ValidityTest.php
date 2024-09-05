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

	class ValidDispatch2 extends DispatchBase {
		public function initialize($input) {
			$this->makeValid();
		}
	}

	class ValidNode extends NodeBase {
		public function __construct() {
			$this->_key     = 'ValidNode';
			$this->_version = '1.0.0';
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			return;
		}
	}

	class InvalidNode extends NodeBase {
		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			return;
		}
	}

	class DispatchTestNode extends NodeBase {
		public function __construct() {
			$this->setKey('DispatchTestNode')->setVersion('1.0.0');
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			return;
		}

		public function testDispatchGood(DispatchBase $dispatch) : bool {
			return $this->isDispatchOfType($dispatch, ValidDispatch::class);
		}

		public function testMultipleDispatchGood(DispatchBase $dispatch) : bool {
			return $this->isDispatchOfType($dispatch, ValidDispatch::class, ValidDispatch2::class);
		}

		public function testDispatchBad(DispatchBase $dispatch) : bool {
			return $this->isDispatchOfType($dispatch, InvalidDispatch::class);
		}

		public function testDispatchBadMultiple(DispatchBase $dispatch) : bool {
			return $this->isDispatchOfType($dispatch, InvalidDispatch::class, ValidDispatch2::class);
		}
	}

	class DispatchTest extends TestCase {
		public function test_dispatchBaseTypes() {
			$validDispatch  = new ValidDispatch();
			$validDispatch2 = new ValidDispatch2();
			$dispatchTest   = new DispatchTestNode();

			self::assertTrue($dispatchTest->testDispatchGood($validDispatch));
			self::assertTrue($dispatchTest->testDispatchBadMultiple($validDispatch2));
			self::assertFalse($dispatchTest->testDispatchBad($validDispatch));
			self::assertFalse($dispatchTest->testDispatchBadMultiple($validDispatch));

			return;
		}

		public function test_invalidNodeGetsRemoved() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new InvalidNode())->linkNode(new ValidNode());

			self::assertCount(1, $chainHelper->getNodeList());

			return;
		}

		public function test_invalidDispatchFails() {
			$chainHelper = new ChainHelper(false, true);
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

<?php

	namespace Stoic\Chain\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;

	class IncrementDispatch extends DispatchBase {
		public function initialize($input = []) {
			$this->makeValid();
			$this->makeConsumable();
		}

		public function increment($number = 0) {
			$number++;

			return $number;
		}
	}

	class StatefulIncrementDispatch extends IncrementDispatch {
		public function initialize($input = []) {
			$this->makeValid();
			$this->makeConsumable();
			$this->makeStateful();
		}
	}

	class IncrementNode extends NodeBase {
		public function __construct() {
			$this->_key = 'IncrementNode';
			$this->_version = '1.0.0';

			return;
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			if (!($dispatch instanceof IncrementDispatch)) {
				return;
			}

			$results = $dispatch->getResults();
			$increment = ($results !== null) ? $results[count($results) - 1]['number'] : 0;
			$number = $dispatch->increment($increment);
			$dispatch->setResult([
				'number' => $number,
			]);

			return;
		}
	}

	class ConsumeNode extends NodeBase {
		public function __construct() {
			$this->_key = 'ConsumeNode';
			$this->_version = '1.0.0';

			return;
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			$dispatch->consume();

			return;
		}
	}

	class InvalidChainHelperNode extends NodeBase {
		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			return;
		}
	}

	class ChainTest extends TestCase {
		public function test_chainExecution() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new IncrementNode())->linkNode(new IncrementNode())->linkNode(new IncrementNode());

			$dispatch = new IncrementDispatch();
			$dispatch->initialize();

			$isChainSuccessful = $chainHelper->traverse($dispatch);
			$results           = $dispatch->getResults();

			self::assertTrue($isChainSuccessful);
			self::assertCount(1, $results);
			self::assertCount(3, $chainHelper->getNodeList());
			self::assertEquals(3, isset($results[0]['number']) ? $results[0]['number'] : 0);

			return;
		}

		public function test_dispatchCanBeConsumed() {
			$dispatch = new IncrementDispatch();
			$dispatch->initialize();

			$chainHelper = new ChainHelper(false, true);
			$chainHelper->linkNode(new InvalidChainHelperNode());
			$chainHelper->traverse($dispatch);
			$chainHelper->linkNode(new IncrementNode())->linkNode(new ConsumeNode())->linkNode(new IncrementNode());

			self::assertFalse($chainHelper->isEvent());
			self::assertNotNull($dispatch->getCalledDateTime());
			self::assertEquals(0, $dispatch->numResults());
			self::assertFalse($dispatch->isStateful());

			$chainHelper->traverse($dispatch);
			$results = $dispatch->getResults();

			self::assertTrue($dispatch->isConsumable());
			self::assertTrue($dispatch->isConsumed());
			self::assertEquals(1, isset($results[0]['number']) ? $results[0]['number'] : 0);

			$chainHelper->traverse($dispatch);

			self::assertTrue($dispatch->isConsumable());
			self::assertTrue($dispatch->isConsumed());
			self::assertEquals(1, isset($results[0]['number']) ? $results[0]['number'] : 0);

			return;
		}

		public function test_dispatchHoldsStates() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new IncrementNode())->linkNode(new IncrementNode())->linkNode(new IncrementNode());

			$dispatch = new StatefulIncrementDispatch();
			$dispatch->initialize();

			$chainHelper->traverse($dispatch);
			$results = $dispatch->getResults();

			self::assertCount(3, $results);
			self::assertEquals(1, isset($results[0]['number']) ? $results[0]['number'] : 0);
			self::assertEquals(2, isset($results[1]['number']) ? $results[1]['number'] : 0);
			self::assertEquals(3, isset($results[2]['number']) ? $results[2]['number'] : 0);

			return;
		}
	}

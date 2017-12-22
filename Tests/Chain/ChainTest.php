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

	class IncrementNode extends NodeBase {
		public function __construct() {
			$this->_key     = 'IncrementNode';
			$this->_version = '1.0.0';
		}

		public function process($sender, DispatchBase &$dispatch) {
			if (!($dispatch instanceof IncrementDispatch)) {
				return;
			}

			$results = $dispatch->getResults();
			$increment = ($results !== null) ? $results[count($results) - 1]['number'] : 0;
			$number  = $dispatch->increment($increment);
			$dispatch->setResult([
				'number' => $number,
			]);
		}
	}

	class ConsumeNode extends NodeBase {
		public function __construct() {
			$this->_key     = 'ConsumeNode';
			$this->_version = '1.0.0';
		}

		public function process($sender, DispatchBase &$dispatch) {
			$dispatch->consume();
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

			$this->assertTrue($isChainSuccessful);
			$this->assertCount(1, $results);
			$this->assertCount(3, $chainHelper->getNodeList());
			$this->assertEquals(3, isset($results[0]['number']) ? $results[0]['number'] : 0);
		}

		public function test_dispatchCanBeConsumed() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new IncrementNode())->linkNode(new ConsumeNode())->linkNode(new IncrementNode());

			$dispatch = new IncrementDispatch();
			$dispatch->initialize();

			$chainHelper->traverse($dispatch);
			$results = $dispatch->getResults();

			$this->assertTrue($dispatch->isConsumable());
			$this->assertTrue($dispatch->isConsumed());
			$this->assertEquals(1, isset($results[0]['number']) ? $results[0]['number'] : 0);
		}

		public function test_dispatchHoldsStates() {
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new IncrementNode())->linkNode(new IncrementNode())->linkNode(new IncrementNode());

			$dispatch = new IncrementDispatch();
			$dispatch->makeStateful();
			$dispatch->initialize();

			$chainHelper->traverse($dispatch);
			$results = $dispatch->getResults();

			$this->assertCount(3, $results);
			$this->assertEquals(1, isset($results[0]['number']) ? $results[0]['number'] : 0);
			$this->assertEquals(2, isset($results[1]['number']) ? $results[1]['number'] : 0);
			$this->assertEquals(3, isset($results[2]['number']) ? $results[2]['number'] : 0);
		}
	}

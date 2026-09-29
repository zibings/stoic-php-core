<?php

	namespace Stoic\Chain\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Chain\ChainHelper;
	use Stoic\Chain\DispatchBase;
	use Stoic\Chain\NodeBase;

	/**
	 * Simple shared journal so nodes can record the order they were processed/unprocessed in.
	 */
	class RevertJournal {
		public array $entries = [];

		public function add(string $entry) : void {
			$this->entries[] = $entry;

			return;
		}
	}

	class RevertDispatch extends DispatchBase {
		public function initialize(mixed $input = null) {
			$this->makeValid();
		}
	}

	class ConsumableRevertDispatch extends DispatchBase {
		public function initialize(mixed $input = null) {
			$this->makeValid();
			$this->makeConsumable();
		}
	}

	/**
	 * Node that journals every process()/unprocess() call and can optionally fail and/or consume the dispatch.
	 */
	class RevertJournalNode extends NodeBase {
		public function __construct(
			protected string $name,
			protected RevertJournal $journal,
			protected bool $doFail = false,
			protected bool $doConsume = false) {
			$this->setKey($name)->setVersion('1.0.0');

			return;
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			$this->journal->add("process:{$this->name}");

			if ($this->doConsume) {
				$dispatch->consume();
			}

			if ($this->doFail) {
				$dispatch->fail();
			}

			return;
		}

		public function unprocess(mixed $sender, DispatchBase &$dispatch) : void {
			$this->journal->add("unprocess:{$this->name}");

			return;
		}
	}

	/**
	 * Node that leaves NodeBase::unprocess() alone so the default no-op behavior can be tested.
	 */
	class RevertNoOverrideNode extends NodeBase {
		public function __construct(protected bool $doFail = false) {
			$this->setKey('RevertNoOverrideNode')->setVersion('1.0.0');

			return;
		}

		public function process(mixed $sender, DispatchBase &$dispatch) : void {
			$dispatch->setResult('processed');

			if ($this->doFail) {
				$dispatch->fail();
			}

			return;
		}
	}

	class RevertTest extends TestCase {
		public function test_dispatchStartsUnfailed() {
			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertFalse($dispatch->isFailed());

			return;
		}

		public function test_dispatchCanOnlyFailOnce() {
			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($dispatch->fail());
			self::assertTrue($dispatch->isFailed());
			self::assertFalse($dispatch->fail());
			self::assertTrue($dispatch->isFailed());

			return;
		}

		public function test_chainIsNotRevertableByDefault() {
			$chainHelper = new ChainHelper();

			self::assertFalse($chainHelper->isRevertable());

			return;
		}

		public function test_revertableCanBeToggled() {
			$chainHelper = new ChainHelper();

			self::assertSame($chainHelper, $chainHelper->toggleRevertable(true));
			self::assertTrue($chainHelper->isRevertable());

			$chainHelper->toggleRevertable(false);

			self::assertFalse($chainHelper->isRevertable());

			return;
		}

		public function test_failedChainRevertsProcessedNodesInReverse() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)
				->linkNode(new RevertJournalNode('A', $journal))
				->linkNode(new RevertJournalNode('B', $journal))
				->linkNode(new RevertJournalNode('C', $journal, doFail: true))
				->linkNode(new RevertJournalNode('D', $journal));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertTrue($dispatch->isFailed());
			self::assertEquals([
				'process:A',
				'process:B',
				'process:C',
				'unprocess:C',
				'unprocess:B',
				'unprocess:A'
			], $journal->entries);

			return;
		}

		public function test_nodesAfterFailureAreNeverProcessed() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)
				->linkNode(new RevertJournalNode('A', $journal, doFail: true))
				->linkNode(new RevertJournalNode('B', $journal));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();
			$chainHelper->traverse($dispatch);

			self::assertNotContains('process:B', $journal->entries);
			self::assertNotContains('unprocess:B', $journal->entries);
			self::assertEquals(['process:A', 'unprocess:A'], $journal->entries);

			return;
		}

		public function test_nonRevertableChainIgnoresFailure() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->linkNode(new RevertJournalNode('A', $journal))
				->linkNode(new RevertJournalNode('B', $journal, doFail: true))
				->linkNode(new RevertJournalNode('C', $journal));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertTrue($dispatch->isFailed());
			self::assertEquals(['process:A', 'process:B', 'process:C'], $journal->entries);

			return;
		}

		public function test_revertableChainDoesNotRevertWithoutFailure() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)
				->linkNode(new RevertJournalNode('A', $journal))
				->linkNode(new RevertJournalNode('B', $journal));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertFalse($dispatch->isFailed());
			self::assertEquals(['process:A', 'process:B'], $journal->entries);

			return;
		}

		public function test_consumedDispatchStillRevertsWhenFailed() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)
				->linkNode(new RevertJournalNode('A', $journal))
				->linkNode(new RevertJournalNode('B', $journal, doFail: true, doConsume: true))
				->linkNode(new RevertJournalNode('C', $journal));

			$dispatch = new ConsumableRevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertTrue($dispatch->isConsumed());
			self::assertEquals([
				'process:A',
				'process:B',
				'unprocess:B',
				'unprocess:A'
			], $journal->entries);

			return;
		}

		public function test_consumingNodeStopsChainWithoutReverting() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)
				->linkNode(new RevertJournalNode('A', $journal))
				->linkNode(new RevertJournalNode('B', $journal, doConsume: true))
				->linkNode(new RevertJournalNode('C', $journal));

			$dispatch = new ConsumableRevertDispatch();
			$dispatch->initialize();
			$chainHelper->traverse($dispatch);

			self::assertTrue($dispatch->isConsumed());
			self::assertFalse($dispatch->isFailed());
			self::assertEquals(['process:A', 'process:B'], $journal->entries);

			return;
		}

		public function test_eventChainRevertsItsNode() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper(true);
			$chainHelper->toggleRevertable(true)->linkNode(new RevertJournalNode('Event', $journal, doFail: true));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertTrue($chainHelper->isEvent());
			self::assertEquals(['process:Event', 'unprocess:Event'], $journal->entries);

			return;
		}

		public function test_eventChainDoesNotRevertWithoutFailure() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper(true);
			$chainHelper->toggleRevertable(true)->linkNode(new RevertJournalNode('Event', $journal));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();
			$chainHelper->traverse($dispatch);

			self::assertEquals(['process:Event'], $journal->entries);

			return;
		}

		public function test_nonRevertableEventChainIgnoresFailure() {
			$journal     = new RevertJournal();
			$chainHelper = new ChainHelper(true);
			$chainHelper->linkNode(new RevertJournalNode('Event', $journal, doFail: true));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();
			$chainHelper->traverse($dispatch);

			self::assertTrue($dispatch->isFailed());
			self::assertEquals(['process:Event'], $journal->entries);

			return;
		}

		public function test_defaultUnprocessIsANoOp() {
			$chainHelper = new ChainHelper();
			$chainHelper->toggleRevertable(true)->linkNode(new RevertNoOverrideNode(true));

			$dispatch = new RevertDispatch();
			$dispatch->initialize();

			self::assertTrue($chainHelper->traverse($dispatch));
			self::assertTrue($dispatch->isFailed());
			self::assertEquals(['processed'], $dispatch->getResults());

			return;
		}
	}

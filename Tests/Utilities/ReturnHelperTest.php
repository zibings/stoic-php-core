<?php

	namespace Stoic\Utilities\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\ReturnHelper;

	class ReturnHelperTest extends TestCase {
		public function test_MessageHandling() {
			$ret = new ReturnHelper();
			self::assertEquals(0, count($ret->getMessages()), "ReturnHelper returns the correct number of messages");
			self::assertFalse($ret->hasMessages(), "ReturnHelper notes absence of messages correctly");

			$ret->addMessage("Testing");
			self::assertEquals(1, count($ret->getMessages()), "ReturnHelper returns the correct number of messages");

			$ret->addMessages(array(
				"Testing2",
				"Testing3"
			));
			self::assertEquals(3, count($ret->getMessages()), "ReturnHelper returns the correct number of messages");
			self::assertTrue($ret->hasMessages(), "ReturnHelper notes presence of messages correctly");
			self::assertArraySubset(array("Testing", "Testing2", "Testing3"), $ret->getMessages(), true, "ReturnHelper returns the correct messages");

			try {
				$ret->addMessages([]);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Messages array to ReturnHelper::addMessages() must be array with elements", $ex->getMessage());
			}

			return;
		}

		public function test_ResultHandling() {
			$ret = new ReturnHelper();
			self::assertEquals(0, count($ret->getResults()), "ReturnHelper returns the correct number of results");
			self::assertFalse($ret->hasResults(), "ReturnHelper notes absence of results correctly");

			$ret->addResult("Testing");
			self::assertEquals(1, count($ret->getResults()), "ReturnHelper returns the correct number of results");

			$ret->addResults(array(
				"Testing2",
				"Testing3"
			));
			self::assertEquals(3, count($ret->getResults()), "ReturnHelper returns the correct number of results");
			self::assertTrue($ret->hasResults(), "ReturnHelper notes presence of results correctly");
			self::assertArraySubset(array("Testing", "Testing2", "Testing3"), $ret->getResults(), true, "ReturnHelper returns the correct results");

			try {
				$ret->addResults([]);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Results array to ReturnHelper::addResults() must be array with elements", $ex->getMessage());
			}

			return;
		}

		public function test_GoodVsBad() {
			$ret = new ReturnHelper();
			self::assertTrue($ret->isBad(), "ReturnHelper initializes as STATUS_BAD");

			$ret->makeGood();
			self::assertTrue($ret->isGood(), "ReturnHelper is made STATUS_GOOD");

			$ret->makeBad();
			self::assertTrue($ret->isBad(), "ReturnHelper is made STATUS_BAD");

			return;
		}
	}

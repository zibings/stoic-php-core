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

			$ret->addMessages(["Testing2", "Testing3"]);
			self::assertEquals(3, count($ret->getMessages()), "ReturnHelper returns the correct number of messages");
			self::assertTrue($ret->hasMessages(), "ReturnHelper notes presence of messages correctly");

			$messages = $ret->getMessages();
			self::assertEquals("Testing", $messages[0], "ReturnHelper returned the correct messages");
			self::assertEquals("Testing2", $messages[1], "ReturnHelper returned the correct messages");
			self::assertEquals("Testing3", $messages[2], "ReturnHelper returned the correct messages");

			try {
				$ret->addMessages([]);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Messages array to ReturnHelper::addMessages() must be array with elements", $ex->getMessage());
			}

			return;
		}

		public function test_MessageReversal() {
			$ret = new ReturnHelper();
			self::assertEquals([], $ret->getMessages(true), "ReturnHelper returns empty array when reversing no messages");

			$ret->addMessage("First");
			$ret->addMessage("Second");
			$ret->addMessage("Third");

			self::assertEquals(["First", "Second", "Third"], $ret->getMessages(), "ReturnHelper returns messages in insertion order by default");
			self::assertEquals(["First", "Second", "Third"], $ret->getMessages(false), "ReturnHelper returns messages in insertion order when not reversed");
			self::assertEquals(["Third", "Second", "First"], $ret->getMessages(true), "ReturnHelper returns messages in reverse insertion order when reversed");

			$reversed = $ret->getMessages(true);
			self::assertSame(0, array_key_first($reversed), "ReturnHelper reversed messages are re-indexed from zero");
			self::assertEquals(["First", "Second", "Third"], $ret->getMessages(), "ReturnHelper reversal does not mutate internal message order");

			return;
		}

		public function test_MessageWeighting() {
			$ret = new ReturnHelper();
			self::assertEquals([], $ret->getMessagesWeighted(), "ReturnHelper returns empty array when weighting no messages");
			self::assertEquals([], $ret->getMessagesWeighted(true), "ReturnHelper returns empty array when reverse weighting no messages");

			$ret->addMessage("Heavy", 10);
			$ret->addMessage("Light", 1);
			$ret->addMessage("Medium", 5);
			$ret->addMessage("Negative", -3);

			self::assertEquals(["Heavy", "Light", "Medium", "Negative"], $ret->getMessages(), "ReturnHelper keeps insertion order for unweighted retrieval");
			self::assertEquals(["Negative", "Light", "Medium", "Heavy"], $ret->getMessagesWeighted(), "ReturnHelper orders messages by ascending weight");
			self::assertEquals(["Negative", "Light", "Medium", "Heavy"], $ret->getMessagesWeighted(false), "ReturnHelper orders messages by ascending weight when not reversed");
			self::assertEquals(["Heavy", "Medium", "Light", "Negative"], $ret->getMessagesWeighted(true), "ReturnHelper orders messages by descending weight when reversed");

			self::assertEquals(["Heavy", "Light", "Medium", "Negative"], $ret->getMessages(), "ReturnHelper weighted retrieval does not mutate internal message order");

			return;
		}

		public function test_MessageWeightingDefaults() {
			$ret = new ReturnHelper();
			$ret->addMessage("Default");
			$ret->addMessage("Zero", 0);
			$ret->addMessage("Two", 2);

			self::assertEquals(["Zero", "Default", "Two"], $ret->getMessagesWeighted(), "ReturnHelper::addMessage() defaults message weight to 1");

			return;
		}

		public function test_MessageWeightingStability() {
			$ret = new ReturnHelper();
			$ret->addMessage("A", 2);
			$ret->addMessage("B", 1);
			$ret->addMessage("C", 2);
			$ret->addMessage("D", 1);
			$ret->addMessage("E", 2);

			self::assertEquals(["B", "D", "A", "C", "E"], $ret->getMessagesWeighted(), "ReturnHelper preserves insertion order for messages of equal weight");
			self::assertEquals(["E", "C", "A", "D", "B"], $ret->getMessagesWeighted(true), "ReturnHelper fully reverses the weighted order, including equal-weight messages");

			$ret2 = new ReturnHelper();
			$ret2->addMessages(["One", "Two", "Three"]);
			self::assertEquals(["One", "Two", "Three"], $ret2->getMessagesWeighted(), "ReturnHelper keeps insertion order when all weights are equal");
			self::assertEquals(["Three", "Two", "One"], $ret2->getMessagesWeighted(true), "ReturnHelper reverses insertion order when all weights are equal and reversed");

			return;
		}

		public function test_AddMessagesWithPairs() {
			$ret = new ReturnHelper();
			$ret->addMessages([
				["Paired heavy", 10],
				"Plain",
				["Paired light", -1],
			]);

			self::assertEquals(["Paired heavy", "Plain", "Paired light"], $ret->getMessages(), "ReturnHelper::addMessages() keeps insertion order for mixed strings and pairs");
			self::assertEquals(["Paired light", "Plain", "Paired heavy"], $ret->getMessagesWeighted(), "ReturnHelper::addMessages() honors weights from message pairs and defaults plain strings to weight 1");

			return;
		}

		public function test_AddMessagesDefaultWeight() {
			$ret = new ReturnHelper();
			$ret->addMessage("Weight 3", 3);
			$ret->addMessages(["Default A", "Default B"], 5);
			$ret->addMessage("Weight 4", 4);
			$ret->addMessages([["Explicit", 2], "Default C"], 5);

			self::assertEquals(
				["Explicit", "Weight 3", "Weight 4", "Default A", "Default B", "Default C"],
				$ret->getMessagesWeighted(),
				"ReturnHelper::addMessages() applies default weight only to plain string messages"
			);

			return;
		}

		public function test_AddMessagesSkipsInvalidEntries() {
			$ret = new ReturnHelper();
			$ret->addMessages([
				"Valid string",
				["Valid pair", 2],
				["Too short"],
				["Too", "long", 3],
				[],
				42,
				3.14,
				null,
				true,
				new \stdClass(),
			]);

			self::assertEquals(2, count($ret->getMessages()), "ReturnHelper::addMessages() ignores non-string and malformed pair entries");
			self::assertEquals(["Valid string", "Valid pair"], $ret->getMessages(), "ReturnHelper::addMessages() keeps only valid entries");
			self::assertEquals(["Valid string", "Valid pair"], $ret->getMessagesWeighted(), "ReturnHelper::addMessages() weights only valid entries");

			$ret2 = new ReturnHelper();
			$ret2->addMessages([["Only invalid"], 7]);
			self::assertFalse($ret2->hasMessages(), "ReturnHelper::addMessages() adds nothing when every entry is invalid");

			return;
		}

		public function test_ResultHandling() {
			$ret = new ReturnHelper();
			self::assertEquals(0, count($ret->getResults()), "ReturnHelper returns the correct number of results");
			self::assertFalse($ret->hasResults(), "ReturnHelper notes absence of results correctly");

			$ret->addResult("Testing");
			self::assertEquals(1, count($ret->getResults()), "ReturnHelper returns the correct number of results");

			$ret->addResults(["Testing2", "Testing3"]);
			self::assertEquals(3, count($ret->getResults()), "ReturnHelper returns the correct number of results");
			self::assertTrue($ret->hasResults(), "ReturnHelper notes presence of results correctly");

			$results = $ret->getResults();
			self::assertEquals("Testing", $results[0], "ReturnHelper returned the correct messages");
			self::assertEquals("Testing2", $results[1], "ReturnHelper returned the correct messages");
			self::assertEquals("Testing3", $results[2], "ReturnHelper returned the correct messages");

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

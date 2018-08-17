<?php

	namespace Stoic\Utilities\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\ReturnHelper;

	class ReturnHelperTest extends TestCase {
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

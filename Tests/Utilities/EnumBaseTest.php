<?php

	namespace Stoic\Utilities\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Utilities\EnumBase;

	class AnEnum extends EnumBase {
		const FIRST_VALUE = 1;
		const SECOND_VALUE = 2;
		const THIRD_VALUE = 3;
		const FOURTH_VALUE = 4;
	}

	class AnotherEnum extends EnumBase {
		const FIRST_VALUE = 1;
	}

	class EnumBaseTest extends TestCase {
		public function test_Instantiation() {
			$val = new AnEnum(AnEnum::FIRST_VALUE);
			self::assertTrue($val->is(AnEnum::FIRST_VALUE));

			self::assertEquals('FIRST_VALUE', $val->getName());
			self::assertEquals(AnEnum::FIRST_VALUE, $val->getValue());

			$val = new AnEnum(5);
			self::assertFalse($val->is(5));

			$val = AnEnum::fromString('FIRST_VALUE');
			self::assertTrue($val->is(AnEnum::FIRST_VALUE));

			$val = AnEnum::fromString('');
			self::assertNull($val->getName());

			return;
		}

		public function test_ConstList() {
			self::assertEquals(4, count(AnEnum::getConstList()['name']));
			self::assertEquals(1, count(AnotherEnum::getConstList()['name']));

			return;
		}

		public function test_Serialization() {
			$val = new AnEnum(AnEnum::FIRST_VALUE);
			self::assertEquals('"FIRST_VALUE"', json_encode($val));

			$val = new AnEnum(AnEnum::FIRST_VALUE, false);
			self::assertEquals('"1"', json_encode($val));

			return;
		}
	}

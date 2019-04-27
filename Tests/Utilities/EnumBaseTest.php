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

	class NotAnEnum {
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

		public function test_TryGetEnum() {
			$enum = EnumBase::tryGetEnum(1, AnotherEnum::class);
			self::assertTrue($enum->getValue() == 1);

			$enum = EnumBase::tryGetEnum(new AnotherEnum(1), AnotherEnum::class);
			self::assertTrue($enum->getValue() == 1);

			$enum = EnumBase::tryGetEnum(35, AnotherEnum::class);
			self::assertTrue($enum->getValue() === null);

			$enum = EnumBase::tryGetEnum(null, AnotherEnum::class);
			self::assertTrue($enum->getValue() === null);

			try {
				$enum = EnumBase::tryGetEnum(1, NotAnEnum::class);
				self::assertTrue(false);
			} catch (\InvalidArgumentException $ex) {
				self::assertEquals("Cannot attempt to retrieve an enum from a class that doesn't extend EnumBase", $ex->getMessage());
			}

			return;
		}

		public function test_IsIn() {
			$enum = new AnotherEnum();
			self::assertFalse($enum->isIn(1, 2));

			$enum = new AnotherEnum(AnotherEnum::FIRST_VALUE);
			self::assertTrue($enum->isIn(1));
			self::assertFalse($enum->isIn(2));

			return;
		}
	}

<?php

	namespace Stoic\Log\Tests;

	use PHPUnit\Framework\TestCase;
	use Stoic\Chain\DispatchBase;
	use Stoic\Log\AppenderBase;
	use Stoic\Log\Logger;
	use Stoic\Log\Message;
	use Stoic\Log\MessageDispatch;
	use Stoic\Log\NullAppender;

	class MemoryAppender extends AppenderBase {
		public $messages = array();

		public function __construct() {
			$this->setKey('MemoryAppender');
			$this->setVersion('1.0.0');

			return;
		}

		public function process($sender, DispatchBase &$dispatch) {
			$this->messages = array_merge($dispatch->messages, $this->messages);

			return;
		}
	}

	class TestContextClassWithToString {
		public $status = 5;

		public function __toString() {
			return "TestContextClass: {$this->status}";
		}
	}

	class TestContextClassWithoutToString {
		public $status = 5;
	}

	class LoggerTest extends TestCase {
		public function test_LogLevels() {
			try {
				$msg = new Message('nonexistent-level', 'testing');
				self::assertTrue(false);
			} catch (\Psr\Log\InvalidArgumentException $ex) {
				self::assertEquals("Invalid log level provided to Stoic\Log\LogMessage: nonexistent-level", $ex->getMessage());
			}

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->alert('Testing');
			$log->output();
			
			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::ALERT, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"ALERT\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'ALERT', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->critical('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::CRITICAL, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"CRITICAL\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'CRITICAL', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->debug('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::DEBUG, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"DEBUG\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'DEBUG', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->emergency('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::EMERGENCY, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals(3, count($app->messages[0]->__toArray()));
			self::assertEquals("{ \"level\": \"EMERGENCY\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'EMERGENCY', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->error('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::ERROR, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"ERROR\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'ERROR', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->info('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::INFO, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"INFO\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'INFO', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->notice('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::NOTICE, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"NOTICE\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'NOTICE', 'Testing'), $app->messages[0]->__toString());

			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG, array($app));
			
			$log->warning('Testing');
			$log->output();

			self::assertEquals(1, count($app->messages));
			self::assertEquals(\Psr\Log\LogLevel::WARNING, $app->messages[0]->level);
			self::assertEquals('Testing', $app->messages[0]->message);

			$ts = $app->messages[0]->getTimestamp()->format('Y-m-d G:i:s.u');
			self::assertEquals("{ \"level\": \"WARNING\", \"message\": \"Testing\", \"timestamp\": \"{$ts}\" }", $app->messages[0]->__toJson());
			self::assertEquals(sprintf("%s %' -9s %s", $ts, 'WARNING', 'Testing'), $app->messages[0]->__toString());

			return;
		}

		public function test_LogInterpolate() {
			$app = new MemoryAppender();
			$log = new Logger(\Psr\Log\LogLevel::DEBUG);
			$log->addAppender($app);
			$log->output();

			$log->info('Testing the way we {replace} strings.', array('replace' => 'REPLACE'));
			$log->output();

			self::assertEquals('Testing the way we REPLACE strings.', $app->messages[0]->message);
			$app->messages = array();

			$ex = new \Exception('Testing');
			$log->info('{exception}', array('exception' => $ex));
			$log->output();

			self::assertEquals("[exception Exception]\n\tMessage: Testing\n\tStack Trace: {$ex->getTraceAsString()}", $app->messages[0]->message);
			$app->messages = array();

			$log->info('{obj}', array('obj' => new TestContextClassWithToString()));
			$log->output();

			self::assertEquals("TestContextClass: 5", $app->messages[0]->message);
			$app->messages = array();

			$now = new \DateTime('now', new \DateTimeZone('UTC'));
			$log->info('{date}', array('date' => $now));
			$log->output();

			self::assertEquals($now->format(\DateTime::RFC3339), $app->messages[0]->message);
			$app->messages = array();

			$log->info('{obj}', array('obj' => new TestContextClassWithoutToString()));
			$log->output();

			self::assertEquals('[object Stoic\Log\Tests\TestContextClassWithoutToString]', $app->messages[0]->message);
			$app->messages = array();

			$log->info('{obj}', array('obj' => null));
			$log->output();

			self::assertEquals('null', $app->messages[0]->message);
			$app->messages = array();

			return;
		}

		public function test_BadMinimumLevel() {
			self::expectException(\InvalidArgumentException::class);
			$log = new Logger('boom');
			$log = new Logger(1);

			return;
		}

		public function test_MessageDispatch() {
			$msg = new Message(\Psr\Log\LogLevel::ALERT, 'Testing');

			$disp = new MessageDispatch();
			$disp->initialize('testing');
			self::assertFalse($disp->isValid());

			$disp = new MessageDispatch();
			$disp->initialize([]);
			self::assertFalse($disp->isValid());

			$disp = new MessageDispatch();
			$disp->initialize([$msg]);
			self::assertTrue($disp->isValid());

			$disp = new MessageDispatch();
			$disp->initialize($msg);
			self::assertTrue($disp->isValid());

			return;
		}
	}

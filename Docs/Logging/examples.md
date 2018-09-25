## Examples
- [Logger Examples](#logger-examples)
  - [Basic Logger Usage](#basic-logger-usage)
  - [Logger Usage With Level](#logger-usage-with-level)
  - [Logger Context Interpolation](#logger-context-interpolation)
- [Appender Examples](#appender-examples)
  - [Basic Appender](#basic-appender)
  - [Adding Appenders](#adding-appenders)

### Logger Examples
#### Basic Logger Usage
```php
    use Stoic\Log\Logger;

    $log = new Logger();
    $log->emergency("Testing emergency logging");
    $log->alert("Testing alert logging");
    $log->critical("Testing critical logging");
    $log->error("Testing error logging");
    $log->warning("Testing warning logging");
    $log->notice("Testing notice logging");
    $log->info("Testing info logging");
    $log->debug("Testing debug logging");

/*

Logger now produces this message collection, from memory:
    [
	    0 => Stoic\Log\Message { "level": "EMERGENCY", "message": "Testing emergency logging", "timestamp": "2018-09-25 17:10:40.518600" },
	    1 => Stoic\Log\Message { "level": "ALERT", "message": "Testing alert logging", "timestamp": "2018-09-25 17:10:40.518900" },
	    2 => Stoic\Log\Message { "level": "CRITICAL", "message": "Testing critical logging", "timestamp": "2018-09-25 17:10:40.519000" },
	    3 => Stoic\Log\Message { "level": "ERROR", "message": "Testing error logging", "timestamp": "2018-09-25 17:10:40.519000" },
	    4 => Stoic\Log\Message { "level": "WARNING", "message": "Testing warning logging", "timestamp": "2018-09-25 17:10:40.519100" },
	    5 => Stoic\Log\Message { "level": "NOTICE", "message": "Testing notice logging", "timestamp": "2018-09-25 17:10:40.519100" },
	    6 => Stoic\Log\Message { "level": "INFO", "message": "Testing info logging", "timestamp": "2018-09-25 17:10:40.519200" },
	    7 => Stoic\Log\Message { "level": "DEBUG", "message": "Testing debug logging", "timestamp": "2018-09-25 17:10:40.519200" }
    ]

*/
```

#### Logger Usage With Level
```php
    use Stoic\Log\Logger;

    $log = new Logger(\Psr\Log\LogLevel::WARNING);
    $log->emergency("Testing emergency logging");
    $log->alert("Testing alert logging");
    $log->critical("Testing critical logging");
    $log->error("Testing error logging");
    $log->warning("Testing warning logging");
    $log->notice("Testing notice logging");
    $log->info("Testing info logging");
    $log->debug("Testing debug logging");

/*

Logger now produces this message collection, from memory (notice we're missing anything less severe than a warning):
    [
        0 => Stoic\Log\Message { "level": "EMERGENCY", "message": "Testing emergency logging", "timestamp": "2018-09-25 17:17:28.449400" }
        1 => Stoic\Log\Message { "level": "ALERT", "message": "Testing alert logging", "timestamp": "2018-09-25 17:17:28.449600" }
        2 => Stoic\Log\Message { "level": "CRITICAL", "message": "Testing critical logging", "timestamp": "2018-09-25 17:17:28.449600" }
        3 => Stoic\Log\Message { "level": "ERROR", "message": "Testing error logging", "timestamp": "2018-09-25 17:17:28.449700" }
        4 => Stoic\Log\Message { "level": "WARNING", "message": "Testing warning logging", "timestamp": "2018-09-25 17:17:28.449700" }
    ]

*/
```

#### Logger Context Interpolation
```php
    use Stoic\Log\Logger;

    class DummyClass {
        public $variable = 'value';
    }

    class LessDummyClass {
        public $variable = 'value';

        public function __toString() {
            return "[object LessDummyClass: variable={$this->variable}]";
        }
    }

    $log = new Logger();
    $log->info("This is an exception: {exception}", array('exception' => new Exception("Test exception")));
    $log->info("This is a {something}", array('something' => 'message'));
    $log->info("This is an object: {obj}", array('obj' => new DummyClass()));
    $log->info("This is a smarter object: {obj}", array('obj' => new LessDummyClass()));
    $log->info("And this is a datetime object: {obj}", array('obj' => new DateTime()));

/*

Logger now produces this message collection, from memory (notice we're missing anything less severe than a warning):
    [
        0 => Stoic\Log\Message { "level": "INFO", "message": "This is an exception: [exception Exception]
            Message: Test exception
            Stack Trace: #0 {main}", "timestamp": "2018-09-25 17:24:11.228700" }
        1 => Stoic\Log\Message { "level": "INFO", "message": "This is a message", "timestamp": "2018-09-25 17:24:11.228900" }
        2 => Stoic\Log\Message { "level": "INFO", "message": "This is an object: [object DummyClass]", "timestamp": "2018-09-25 17:24:11.228900" }
        3 => Stoic\Log\Message { "level": "INFO", "message": "This is a smarter object: [object LessDummyClass: variable=value]", "timestamp": "2018-09-25 17:24:11.229000" }
        4 => Stoic\Log\Message { "level": "INFO", "message": "And this is a datetime object: 2018-09-25T17:24:11-04:00", "timestamp": "2018-09-25 17:24:11.229000" }
    ]

*/
```

### Appender Examples
#### Basic Appender
These appenders will be used in the following examples.

```php
    use Stoic\Chain\DispatchBase;
    use Stoic\Log\AppenderBase;
    use Stoic\Log\MessageDispatch;

    class EchoAppender extends AppenderBase {
        public function __construct() {
            $this->setKey('EchoAppender');
            $this->setVersion('1.0.0');

            return;
        }

        public function process($sender, DispatchBase &$dispatch) {
            if (!($dispatch instanceof MessageDispatch)) {
                return;
            }

            foreach (array_values($dispatch->messages) as $message) {
                echo("{$message}\n");
            }

            return;
        }
    }

    class JsonAppender extends AppenderBase {
        public function __construct() {
            $this->setKey('JsonAppender');
            $this->setVersion('1.0.0');

            return;
        }

        public function process($sender, DispatchBase &$dispatch) {
            if (!($dispatch instanceof MessageDispatch)) {
                return;
            }

            foreach (array_values($dispatch->messages) as $message) {
                echo("{$message->__toJson()}\n");
            }

            return;
        }
    }
```

#### Adding Appenders
```php
    use Psr\Log\LogLevel;
    use Stoic\Log\Logger;

    // Add appenders using constructor
    $log = new Logger(LogLevel::DEBUG, array(new EchoAppender(), new JsonAppender()));
    $log->info("Testing info logging");
    $log->output();

    // Add appenders using addAppender() method
    $log = new Logger();
    $log->addAppender(new EchoAppender());
    $log->addAppender(new JsonAppender());
    $log->info("Testing info logging");
    $log->output();

/*

Both instances produce the same results (excluding different timestamps, of course):

    2018-09-25 17:59:19.599100 INFO      Testing info logging
    { "level": "INFO", "message": "Testing info logging", "timestamp": "2018-09-25 17:59:19.599100" }

*/
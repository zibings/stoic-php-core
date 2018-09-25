# Stoic Core Logging
A logging system that is PSR-3 complient but better suited to configured output.

## Concept
Building on top of the [Chain](../Chains/index.md) system built into Stoic, the Logger retains PSR-3 compatibility by implementing
all appropriate interfaces, but is built in a way that considers output as simply a configurable option on each `Logger` instance.

Since the system IS PSR-3 compatible, we'll focus here on our usage of logging 'appenders' and how they provide easy configurability
to your systems.  For information on PSR-3 logging, please refer for the [PHP FIG website](https://www.php-fig.org/psr/psr-3/).

## End-to-End Example
A fully-functional (and very simplistic) example of a new appender:

```php
<?php

    use Stoic\Chain\DispatchBase;
    use Stoic\Log\AppenderBase;
    use Stoic\Log\Logger;
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

            if (count($dispatch->messages) > 0) {
                foreach (array_values($dispatch->messages) as $message) {
                    echo("{$message}\n");
                }
            }

            return;
        }
    }

    $log = new Logger();
    $log->addAppender(new EchoAppender());

    $log->info("Testing log info output.");
    $log->critical("Testing log critical output.");

    $log->output();

```

## Further Reading
To find out more about the system, check out the following pages:

* [Logger](logger.md)
* [Appenders](appenders.md)
* [Messages](messages.md)
* [All Examples](examples.md)
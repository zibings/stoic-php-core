## Logger Class
The `Logger` class provides the basic functionality set by PSR-3 and guaranteed by the `Psr\Log\AbstractLogger` class.  Additionally
it provides a mechanism to attach one or more [appenders](appenders.md) that implement the `AppenderBase` abstract class and are used
during log output.

### PSR-3 Note
This class, by implementing the `\Psr\Log\AbstractLogger` abstract class, guarantees the existence of the following methods:

- [public] `emergency($message, array $context)`
- [public] `alert($message, array $context)`
- [public] `critical($message, array $context)`
- [public] `error($message, array $context)`
- [public] `warning($message, array $context)`
- [public] `notice($message, array $context)`
- [public] `info($message, array $context)`
- [public] `debug($message, array $context)`

Since these are well documented on the [PHP FIG website](https://www.php-fig.org/psr/psr-3/), we won't go into the details here.

### Properties
- [private:Stoic\Chain\ChainHelper] `$appenders` -> Internal instance of a `Stoic\Chain\ChainHelper` instance to manage appenders for this logger instance
- [private:Message[]] `$messages` -> Collection of log messages encapsulated by the `Message` class
- [protected:static:string[]] `$levels` -> Numerically indexed collection of log levels for 'minimum level' comparisons

### Methods
- [public] `__construct($minimumLevel, array $appenders)` -> Constructor with optional arguments to set minimum log level and provide collection of `AppenderBase` appenders to add immediately
- [public] `addAppender(AppenderBase $appender)` -> Attempts to add (link) an [appender](appenders.md) to the internal `ChainHelper` for output appenders
- [protected] `interpolate($message, array $context)` -> Interpolates context values into a log message
- [public] `log($level, $message, array $context)` -> Stores an arbitrary message & log level into the internal collection as a `Message`
- [protected] `meetsMinimumLevel($level)` -> Determines whether or not a given level is at least as 'high' in importance as the minimum level set at instantiation
- [public] `output()` -> Triggers a dump of all log messages in memory to any configured appenders; Additionally clears internal message collection

### Examples
For examples, please see the 'Logger' section of the [Examples](examples.md) page.
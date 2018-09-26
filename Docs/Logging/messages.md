## Message Class
The `Message` class provides some structure and immutable time storage for log messages.  Any message
stored inside a `Message` object should have already been passed through context interpolation, thus
rendering the message text in a 'final' state.

### Properties
- [public:string] `$level` -> String value of message's log level
- [public:string] `$message` -> Finalized string value of log message
- [private:\DateTimeImmutable] `$timestamp` -> Immutable DateTime value for time message object was generated (includes microseconds)
- [private:static:array] `$validLevels` -> Collection of valid level values for making sure good information is provided at instantiation

### Methods
- [public] `__construct($level, $message)` -> Instantiates a new Message object, validating the log level and creating a `\DateTimeImmutable`
- [public] `getTimestamp()` -> Returns the immutable timestamp value so it can't be overwritten
- [public] `__toArray()` -> Returns the object information as a simplified array: `['level' => '', 'message' => '', 'timestamp' => '']`
- [public] `__toJson()` -> Returns the object information as a simplified JSON string: `{ 'level': '', 'message': '', 'timestamp': '' }`
- [public] `__toString()` -> Returns the object information as a simplified string
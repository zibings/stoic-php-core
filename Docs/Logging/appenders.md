## Appenders
Appenders in Stoic's logging system are nothing more than `NodeBase` classes that process the [MessageDispatch](message-dispatch.md) class.
This means that all rules from the [Chain](../Chains/index.md) system apply, simplifying the process of setting up new appenders.

### AppenderBase
The `AppenderBase` abstract class simply extends the [NodeBase](../Chains/nodes.md#nodebase) abstract class.  This is done simply to force
some differentiation when implementing logging appenders vs other types of nodes.

### MessageDispatch
All `AppenderBase` nodes will receive the `MessageDispatch` dispatch.  For full details, please see the [MessageDispatch](message-dispatch.md) page.

### NullAppender
The only appender which is included with the default Stoic distribution is the `NullAppender`, which serves as a null-sink for any log messages it is passed.

### Examples
For examples, please see the 'Appenders' section of the [Examples](examples.md) page.
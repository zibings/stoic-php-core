## ChainHelper Class
The `ChainHelper` class provides the basic functionality
associated with the Stoic chain system, specifically linking
nodes together and traversing that 'chain' using a dispatch
that implements the [DispatchBase](dispatches.md#dispatchbase)
abstract class.

### Events vs Chains
The `ChainHelper` class was built to operate with multiple linked
nodes at any given time, but this design also easily lends itself
to use as an event dispatcher.  By telling a `ChainHelper` instance
to be an event, the instance will only allow a single node to be
linked at any given time.  Additional nodes that are linked will
simply overwrite the previously linked node and take its place
as the event callback.

### Properties
- [protected:array] `$_nodes` -> Internal collection of linked nodes
- [protected:boolean] `$_isEvent` -> Whether or not instance is an event-chain
- [protected:boolean] `$_doDebug` -> Whether or not instance should record debug messages
- [protected:callback] `$_logger` -> Optional callback that receives debug messages (if enabled)

### Methods
- [public] `__construct($isEvent, $doDebug)` -> Constructor with ability to override event and debug toggles
- [public] `toggleDebug($doDebug)` -> Toggles use of debug messages by instance
- [public] `getNodeList()` -> Returns full list of nodes linked to the chain
- [public] `hookLogger(callable $callback)` -> Attaches the given callback to the chain for receive debug messages
- [public] `isEvent()` Returns whether or not the instance is setup as an event chain
- [public] `linkNode(NodeBase $node)` -> Registers a valid node which implements `NodeBase` into the chain
- [public] `traverse(DispatchBase &$dispatch, $sender)` -> Begins distributing the given dispatch to any linked nodes, optional sender parameter
- [protected] `log($message)` -> Conditionally sends debug message to registered logger callback

### Examples
For examples, please see the 'ChainHelper' section of the [Examples](examples.md) page.
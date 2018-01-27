## Nodes
Nodes in the Stoic chain system exist to process data (dispatches).
They are connected to each other via a [ChainHelper](chainhelper.md)
instance and receive a [Dispatch](dispatches.md) when they are asked
by the `ChainHelper` to execute their `process()` method.

### NodeBase
All nodes used by the provided [ChainHelper](chainhelper.md) must
implement the `NodeBase` abstract class.  This provides meta
information for the node and requires developers to implement a
`process()` method.  The following are the properties and methods
defined in `NodeBase`:

#### Properties
- [protected:string] `$_key` -> String that identifies the node
- [protected:string] `$_version` -> String that provides the node's version number

#### Methods
- [public] `getKey()` -> Returns the node's key value
- [public] `getVersion()` -> Returns the node's version value
- [public] `isValid()` -> Returns whether or not both key and version values are set for node
- [abstract public] `process($sender, DispatchBase &$dispatch)` -> Abstract method for processing dispatch; Only called if node is valid
- [protected] `setKey($key)` -> Sets the node key value
- [protected] `setVersion($version)` -> Sets the node version value

### Examples
For examples, please see the 'Node' section of the [Examples](examples.md)
page.
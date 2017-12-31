## Dispatches
Dispatches in the Stoic chain system are data.  Any information
that should be carried along a chain should be stored within the
dispatch.  This makes the data available to any node which
receives the dispatch during chain traversal, and allows it to
serve as a container for results returned by any nodes that
record them.

### Concepts
Dispatches by default have three 'states' that can be queries.
Each defaults to `false` and must be set by any dispatch during
initialization.  The states are as follows:

- Consumable
- Stateful
- Valid

#### Consumable
When a dispatch marks itself as consumable, it tells the [ChainHelper](chainhelper.md)
to stop traversing a chain if the dispatch is consumed by a node.
This can be helpful in chains that resemble the "chain-of-responsibility"
pattern.

#### Stateful
All nodes are given the ability to store results in dispatches,
and the dispatch's stateful setting will determine if only one
result is allowed or multiple are kept.  Regardless of this
setting, results are always returned as `null` or an array.

#### Valid
In order for the [ChainHelper](chainhelper.md) to traverse a chain
it must have both nodes and a valid dispatch.  This gives developers
the ability to enforce custom restrictions on data validity at
initialization in order to stop chain traversal.

### DispatchBase
All dispatches must implement the `DispatchBase` abstract class.
This provides some minimum functionality and requires developers
to implement an `initialize()` method.  The following are the
properties and methods defined in `DispatchBase`:

#### Properties
- [protected:boolean] `$_isConsumable` -> Whether or not the dispatch is 'consumable'
- [protected:boolean] `$_isStateful` -> Whether or not the dispatch should retain results
- [protected:boolean] `$_isConsumed` -> Whether or not the dispatch has been consumed by a node
- [protected:array] `$_results` -> Collection of results (state) from nodes
- [protected:boolean] `$_isValid` -> Whether or not the dispatch is valid for processing

#### Methods
- [public] `consume()` Marks dispatch as having been consumed
- [public] `getCalledDateTime()` Returns time dispatch was marked valid
- [public] `getResults()` Returns any results stored in dispatch
- [abstract public] `initialize($input)` Abstract method for handling initialization
- [public] `isConsumable()` Returns whether or not dispatch can be consumed
- [public] `isConsumed()` Returns whether or not dispatch has been consumed
- [public] `isStateful()` Returns whether or not dispatch is stateful
- [public] `isValid()` Returns whether or not dispatch is marked as valid
- [protected] `makeConsumable()` Sets dispatch as consumable
- [protected] `makeStateful()` Sets dispatch as stateful
- [protected] `makeValid()` Sets dispatch as valid
- [public] `numResults()` Returns number of results stored in dispatch
- [public] `setResult($result)` Sets a result in dispatch, overwrites any existing result if dispatch not stateful

### Examples
For examples, please see the 'Dispatch' section of the [Examples](examples.md) page.
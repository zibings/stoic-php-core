## MessageDispatch Class
The `MessageDispatch` class is used to provide collections of [Message](messages.md) objects for consumption
by [appenders](appenders.md).

### Properties
- [public:Message[]] `$messages` -> Collection of any messages returned by the `Logger` instance (possibly filtered by minimum level)

### Methods
- [public] `initialize($input)` -> Initializes the dispatch with message values before being used for chain traversal
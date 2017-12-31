## Examples
- [Dispatch Examples](#dispatch-examples)
  - [Consumable Dispatch](#consumable-dispatch)
  - [Stateful Dispatch](#stateful-dispatch)
  - [An Increment Dispatch](#an-increment-dispatch)
- [Node Examples](#node-examples)
  - [Incrementer Node](#an-incrementer-node)
  - [Consumer Node](#a-consumer-node)
  - [Chatty Node](#a-chatty-node)
- [Chain Examples](#chain-examples)

### Dispatch Examples
The following dispatches will be used for examples later on.

#### Consumable Dispatch
```php
use Stoic\Chain\DispatchBase;

// A simple dispatch that can be consumed
class ConsumableDispatch extends DispatchBase {
    // Implement the abstract method with no real use of $input
    public function initialize($input) {
        // Mark the dispatch as consumable, this way chain traversal
        // can be stopped by a node
        $this->makeConsumable();

        // Exclude this if you have something about your $input that
        // isn't valid for this dispatch's type
        $this->makeValid();

        return;
    }
}
```

#### Stateful Dispatch
```php
use Stoic\Chain\DispatchBase;

// A simple dispatch that is stateful
class StatefulDispatch extends DispatchBase {
    // Implement the abstract method with no real use of $input
    public function initialize($input) {
        // Mark the dispatch as stateful so multiple results can be
        // stored in the dispatch by a node (or nodes)
        $this->makeStateful();

        // Exclude this if you have something about your $input that
        // isn't valid for this dispatch's type
        $this->makeValid();

        return;
    }
}
```

#### An Increment Dispatch
```php
use Stoic\Chain\DispatchBase;

// A simple dispatch that can be consumed
class IncrementDispatch extends DispatchBase {
    // Private integer for our increment
    private $counter = 0;

    // Implement abstract with override for increment number
    public function initialize($input) {
        // Mark the dispatch as consumable
        $this->makeConsumable();

        // if we have input, check if it's valid
        if ($input !== null) {
            if (is_int($input) === true) {
                // it's valid, so set it and prepare for traversal
                $this->counter = $input;
                $this->makeValid();
            }
        } else {
            // we aren't modifying our counter, so prepare for traversal
            $this->makeValid();
        }

        return;
    }

    // So we can see what our counter is at currently
    public function getCounterValue() {
        return $this->counter;
    }

    // A public method to let a node trigger incrementing the counter
    public function incrementCount() {
        $this->counter++;

        return;
    }
}
```

### Node Examples
The following nodes will be used for examples later on.

#### An Incrementer Node
```php
use Stoic\Chain\DispatchBase;
use Stoic\Chain\NodeBase;

// A node that only calls an increment method on the dispatch
class IncrementerNode extends NodeBase {
    // Need to instantiate with key/version info to be valid
    public function __construct() {
        $this->setKey('incrementerNode');
        $this->setVersion('0.0.1');

        return;
    }

    // Implement this to actually perform processing in a chain
    public function process($sender, DispatchBase &$dispatch) {
        if (!($dispatch instanceof IncrementDispatch)) {
            return;
        }

        // Now we're sure it's the dispatch we want, so increment
        // and simply return so the next node in the chain can do
        // its job
        $dispatch->incrementCount();

        return;
    }
}
```

#### A Consumer Node
```php
use Stoic\Chain\DispatchBase;
use Stoic\Chain\NodeBase;

// A node that simply attempts to consume a dispatch
class ConsumerNode extends NodeBase {
    // Need to instantiate with key/version info to be valid
    public function __construct() {
        $this->setKey('consumerNode');
        $this->setVersion('0.0.1');

        return;
    }

    // Implement this to actually perform processing in a chain
    public function process($sender, DispatchBase &$dispatch) {
        // Since we don't care what kind of dispatch (all should
        // have the consume method), just consume and return
        $dispatch->consume();

        return;
    }
}
```

#### A Chatty Node
```php
use Stoic\Chain\DispatchBase;
use Stoic\Chain\NodeBase;

// An IncrementerNode that yells out what it's doing
class ChattyNode extends IncrementerNode {
    public function __construct() {
        $this->setKey('chattyNode');
        $this->setVersion('0.0.1');

        return;
    }

    public function process($sender, DispatchBase &$dispatch) {
        if (!($dispatch instanceof IncrementerDispatch)) {
            return;
        }

        // Call the IncrementerNode process so it does its job
        parent::process($sender, $dispatch);

        // Echo the current counter value after incrementing
        echo($dispatch->getCounterValue());

        return;
    }
}
```

### Chain Examples
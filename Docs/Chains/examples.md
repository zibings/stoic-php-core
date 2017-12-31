## Examples
- [Dispatch Examples](#dispatch-examples)
- [Node Examples](#node-examples)
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

    // A public method to let a node trigger incrementing the counter
    public function incrementCount() {
        $this->counter++;

        return;
    }
}
```

### Node Examples

### Chain Examples
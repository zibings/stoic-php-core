## ReturnHelper Class
The `ReturnHelper` class provides a mechanism for creating
methods and functions that returns more information than
a simple scalar value to indicate success.

### The Problem
Given the following function:

```php
<?php

    function myTest($inputA, $inputB) {
        if ($inputA === null || $inputB === null) {
            return false;
        }

        if ($inputA < $inputB) {
            return false;
        }

        return true;
    }

    if (myTest($varA, $varB) === false) {
        // deal with false
    }
```

This function returns a boolean value indicating some kind
of success, but it also returns the same `false` value as
a result of the guard against null values.

Though returning `false` because of the guard is potentially
valid, it may also be useful to know that the reason for the
`false` was not because of the comparison but the guard.  One
option is of course using an exception, but the `ReturnHelper`
offers another option:

```php
<?php

    function myTest($inputA, $inputB) {
        $ret = new ReturnHelper();

        if ($inputA === null || $inputB === null) {
            $ret->makeBad();
            $ret->addMessage("myTest() received a null valuie");

            return $ret;
        }

        if ($inputA < $inputB) {
            $ret->makeBad();
            $ret->addMessage("inputA is less than inputB");

            return $ret;
        }

        $ret->makeGood();

        return $ret;
    }

    $ret = myTest($varA, $varB);

    if ($ret->isBad()) {
        // deal with false & messages here
    }
```

### Properties
- [protected:array] `$_messages` -> Collection of messages for this return
- [protected:array] `$_results` -> Collection of results for this return
- [protected:integer] `$_status` -> Integer value of return's current status

### Constants
- [integer] `STATUS_BAD` -> Internal status for 'Bad' returns
- [integer] `STATUS_GOOD` -> Internal status for 'Good' returns

### Methods
- [public] `__construct()` -> Constructor that creates a new `ReturnHelper` set as 'Bad'
- [public] `addMessage($message)` -> Adds a message to the return
- [public] `addMessages(array $messages)` -> Adds an array of messages to the return
- [public] `addResult($result)` -> Adds a result value to the return
- [public] `addResults(array $results)` -> Adds an array of results to the return
- [public] `isBad()` -> Returns true if the return's status is 'Bad'
- [public] `isGood()` -> Returns true if the return's status is 'Good'
- [public] `getMessages()` -> Returns the collection of messages for the return
- [public] `getResults()` -> Returns the collection of results for the return
- [public] `hasMessages()` -> Returns true if there are messages in the return's collection
- [public] `hasResults()` -> Returns true if there are results in the return's collection
- [public] `makeBad()` -> Sets the return's status as 'Bad'
- [public] `makeGood()` -> Sets the return's status as 'Good'
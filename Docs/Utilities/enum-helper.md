## EnumBase Class
The `EnumBase` abstract class provides some general functionality
for creating enumerated classes/values within PHP.

### A Quick Example
Creating and using a class of enumerated values is easy:

```php
<?php

	use Stoic\Utilities\EnumBase;

	class Numbers extends EnumBase {
		const ONE = 1;
		const TWO = 2;
		const THREE = 3;
	}

	$num = new Numbers(Numbers::ONE);

	if ($num->is(Numbers::TWO)) {
		echo("This is number 2");
	} else {
		echo("The number is: {$num}");
	}
```

Since enumerated values aren't supported in PHP, using a
class with constants allows for type-checking.  Usage is
simple and both string and JSON serialization are automatically
implemented.

### Static Properties
- [protected:array] `$constCache` -> Static internal cache of const lookups

### Properties
- [protected:string] `$name` -> Internal storage for name
- [protected:integer] `$value` -> Internal storage for value
- [protected:boolean] `$serializeAsName` -> Determines whether or not to serialize as name, defaults to true

### Static Methods
- [public] `fromString($string, $serializeAsName)` -> Returns a new Enum object using the name instead of the value for initialization
- [public] `getConstList()` -> Returns the const lookup for the called class
- [public] `tryGetEnum($value, $className)` -> Returns an EnumBase object of the type `$className`, either using an existing instance given to `$value` or using a valid integer given to `$value`
- [public] `validName($name)` -> Validates a name against the const lookup values for the called class
- [public] `validValue($value)` -> Validates a value against the const lookup values for the called class

### Methods
- [public] `__construct($value, $serializeAsName)` -> Instantiates a new Enum object with an optional value
- [public] `__toString()` -> Serializes the object to its string representation
- [public] `getName()` -> Retrieves the set name for the object
- [public] `getValue()` -> Retrieves the set value for the object
- [public] `is($value)` -> Determines if the set value for the object is the same as the supplied value
- [public] `isIn(...$values)` -> Determines if the current value is equal to any of the supplied values
- [public] `jsonSerialize()` -> Serializes the object to its string representation
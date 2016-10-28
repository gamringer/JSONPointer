JSONPointer
============

[![License](https://poser.pugx.org/gamringer/php-json-pointer/license.svg)](https://packagist.org/packages/gamringer/php-json-pointer)
[![Latest Stable Version](https://poser.pugx.org/gamringer/php-json-pointer/v/stable.svg)](https://packagist.org/packages/gamringer/php-json-pointer)
[![Latest Unstable Version](https://poser.pugx.org/gamringer/php-json-pointer/v/unstable.svg)](https://packagist.org/packages/gamringer/php-json-pointer)
[![Total Downloads](https://poser.pugx.org/gamringer/php-json-pointer/downloads.svg)](https://packagist.org/packages/gamringer/php-json-pointer)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026/mini.png)](https://insight.sensiolabs.com/projects/9f14b6ae-8100-4c43-9084-b17f57165026)

[![Build Status](https://travis-ci.org/gamringer/JSONPointer.svg?branch=master)](https://travis-ci.org/gamringer/JSONPointer)

[![Build Status](https://scrutinizer-ci.com/g/gamringer/JSONPointer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPointer/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/gamringer/JSONPointer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPointer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gamringer/JSONPointer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gamringer/JSONPointer/?branch=master)

A RFC6901 compliant JSON Pointer PHP implementation

#License
JSONPointer is licensed under the MIT license.

#Installation

    composer require gamringer/php-json-pointer

##Tests

    composer install
    phpunit
    
#Documentation

##Testing a value for existence
```php
<?php

$target = [
  "foo" => ["bar", "baz"],
  "qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->has("/foo"));

/* Results:

bool(true)

*/
```
Retrieving a value that does not exist will return false

```php
<?php

$target = [
  "qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->has("/foo"));

/* Results:

bool(false)

*/
```

##Retrieving a value
```php
<?php

$target = [
	"foo" => ["bar", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->get("/foo"));

/* Results:

array(2) {
  [0] =>
  string(3) "bar"
  [1] =>
  string(3) "baz"
}

*/
```
Retrieving a value that does not exist will throw an exception

```php
<?php

$target = [
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->get("/foo"));

/* Results:

Throws gamringer\JSONPointer\Exception

*/
```

##Inserting a value
Inserting a value will returns a VoidValue object if used on an indexed array.

```php
<?php

$target = [
	"foo" => ["bar", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

$value = "waldo";
var_dump($pointer->insert("/foo/1", $value));
var_dump($pointer->get("/foo"));

/* Results:

class gamringer\JSONPointer\VoidValue#6 (2) {
  protected $owner =>
  array(3) {
    ...
  }
  protected $target =>
  string(1) "1"
}
array(3) {
  [0] =>
  string(3) "bar"
  [1] =>
  string(5) "waldo"
  [2] =>
  string(3) "baz"
}

*/
```
If used on anything else, it will behave in the exact same way as get()

```php
<?php

$target = [
	"foo" => ["bar", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

$value = "waldo";
var_dump($pointer->insert("/foo", $value));
var_dump($pointer->get("/foo"));

/* Results:

array(2) {
  [0] =>
  string(3) "bar"
  [1] =>
  string(3) "baz"
}
string(5) "waldo"

*/
```

##Setting a value
Setting a value returns the content previously at that path

```php
<?php

$target = [
	"foo" => ["bar", "waldo", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

$value = "corge";
var_dump($pointer->set("/foo", $value));

/* Results:

array(3) {
  [0] =>
  string(3) "bar"
  [1] =>
  string(5) "waldo"
  [2] =>
  string(3) "baz"
}

*/
```

If the path was attainable, but not set, it will return a VoidValue

```php
<?php

$target = [
	"foo" => ["bar", "waldo", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

$value = "garply";
var_dump($pointer->set("/grault", $value));

/* Results:

class gamringer\JSONPointer\VoidValue#6 (2) {
  protected $owner =>
  array(3) {
    ...
  }
  protected $target =>
  string(6) "grault"
}

*/
```

##Remove a value
Removing a value returns the content previously at that path

```php
<?php

$target = [
	"foo" => ["bar", "waldo", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->remove("/qux"));

/* Results:

string(4) "quux"

*/
```
Removing a value that does not exist will throw an exception

```php
<?php

$target = [
	"foo" => ["bar", "waldo", "baz"],
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->remove("/qux"));

/* Results:

Throws gamringer\JSONPointer\Exception

*/
```

##Operations affect the original object
This affects Remove Operations
```php
<?php

$target = [
  "foo" => ["bar", "waldo", "baz"],
  "qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);
$pointer->remove("/qux");

var_dump($target);

/* Results:

array(1) {
  'foo' =>
  array(3) {
    [0] =>
    string(3) "bar"
    [1] =>
    string(5) "waldo"
    [2] =>
    string(3) "baz"
  }
}

*/
```

This also affects Add Operations
```php
<?php

$target = [
  "foo" => ["bar", "waldo", "baz"],
  "qux" => "quux"
];

$value = "bar";
$pointer = new \gamringer\JSONPointer\Pointer($target);
$pointer->set("/foo", $value);

var_dump($target);

/* Results:

array(2) {
  'foo' =>
  string(3) "bar"
  'qux' =>
  string(4) "quux"
}

*/
```

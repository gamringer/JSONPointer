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

Example
-------

```php
<?php

$target = [
	"foo" => ["bar", "baz"],
	"qux" => "quux"
];

$pointer = new \gamringer\JSONPointer\Pointer($target);

var_dump($pointer->get("/foo")); // ["bar", "baz"]
var_dump($pointer->set("/foo", "corge")); // ["bar", "baz"]

var_dump($pointer->set("/qux", "corge")); // Object {VoidValue}
var_dump($pointer->get("/qux")); // "corge"
var_dump($pointer->remove("/qux")); // "corge"
```

Installation
------------

    composer require gamringer/json-pointer

Tests
-----

    composer install
    phpunit

License
-------

JSONPointer is licensed under the MIT license.
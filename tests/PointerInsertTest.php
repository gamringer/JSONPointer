<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;

class PointerInsertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that ...
     */
    public function testInsertNew()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/1', $value)->get('/1');

        $this->assertEquals($value, $result);
    }

    /**
     * Tests that ...
     */
    public function testInsertLast()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/3', $value)->get('/3');

        $this->assertEquals($value, $result);
    }

    /**
     * Tests that ...
     *
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testInsertAfterLast()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/4', $value)->get('/4');
    }

    /**
     * Tests that ...
     */
    public function testInsertForAppend()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/-', $value)->get('/3');

        $this->assertEquals($value, $result);
    }

    /**
     * Tests that ...
     */
    public function testInsertIntoExisting()
    {
        $target = ['foo' => 'value1', 'bar' => 'value2'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/foo', $value)->get('/foo');

        $this->assertEquals($value, $result);
    }
}

<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;

class PointerInsertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox a new value can be inserted at specific location
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
     * @testdox a new value can be inserted as a last element + 1
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
     * @testdox a new value can not be inserted after the last position + 1
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
     * @testdox a new element can be inserted to the append(/-) position
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
     * @testdox a value can be written over via insertion
     */
    public function testInsertIntoExisting()
    {
        $target = ['foo' => 'value1', 'bar' => 'value2'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/foo', $value)->get('/foo');

        $this->assertEquals($value, $result);
    }

    /**
     * @testdox a null value can be written over via insertion in an array
     */
    public function testInsertIntoExistingNull()
    {
        $target = [null, 1, 2];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->insert('/0', $value)->get('/0');

        $this->assertEquals($value, $result);
    }
}

<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\VoidValue;
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

        $previousValue = $pointer->insert('/1', $value);
        $result = $pointer->get('/1');

        $this->assertEquals($value, $result);
        $this->assertInstanceOf(VoidValue::class, $previousValue);
    }

    /**
     * @testdox a new value can be inserted at specific location in an empty array
     */
    public function testInsertNewIntoEmptyIndexed()
    {
        $target = [];
        $pointer = new Pointer($target);

        $value = 'qux';

        $previousValue = $pointer->insert('/0', $value);
        $result = $pointer->get('/0');
        $this->assertEquals($value, $result);
        $this->assertInstanceOf(VoidValue::class, $previousValue);

        $previousValue = $pointer->insert('/-', $value);
        $result = $pointer->get('/1');
        $this->assertEquals($value, $result);
        $this->assertInstanceOf(VoidValue::class, $previousValue);
    }

    /**
     * @testdox a new value can be inserted at specific location in an empty array
     */
    public function testInsertNewIntoEmptyAssoc()
    {
        $target = [];
        $pointer = new Pointer($target);

        $value = 'qux';

        $previousValue = $pointer->insert('/foo', $value);
        $result = $pointer->get('/foo');

        $this->assertEquals($value, $result);
        $this->assertInstanceOf(VoidValue::class, $previousValue);
    }

    /**
     * @testdox a new value can be inserted as a last element + 1
     */
    public function testInsertLast()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $pointer->insert('/3', $value);
        $result = $pointer->get('/3');

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

        $pointer->insert('/4', $value);
        $result = $pointer->get('/4');
    }

    /**
     * @testdox a new element can be inserted to the append(/-) position
     */
    public function testInsertForAppend()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $pointer->insert('/-', $value);
        $result = $pointer->get('/3');

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

        $pointer->insert('/foo', $value);
        $result = $pointer->get('/foo');

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

        $pointer->insert('/0', $value);
        $result = $pointer->get('/0');

        $this->assertEquals($value, $result);
    }
}

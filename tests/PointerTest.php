<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;

class PointerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->target = [
            "foo" => ["bar", "baz"],
            "qux" => ["quux" => "corge"],
            "" => 0,
            "a/b" => 1,
            "c%d" => 2,
            "e^f" => 3,
            "g|h" => 4,
            "i\\j" => 5,
            "k\"l" => 6,
            " " => 7,
            "m~n" => 8,
            "-" => 9
        ];
        $this->pointer = new Pointer($this->target);
    }

    /**
     * @testdox the pointer correctly stores and returns the target
     */
    public function testStoresTarget()
    {
        $this->assertEquals($this->pointer->getTarget(), $this->target);

        return $this->pointer;
    }

    /**
     * @testdox a value can be retrieved
     * @dataProvider pathProvider
     */
    public function testGetPathValue($path)
    {
        $result = $this->pointer->get($path);

        $this->assertNotNull($result);
    }

    /**
     * @testdox a path can be set to a new value
     */
    public function testSetPathValue()
    {
        $value = 'bar';

        $this->pointer->set('/foo/1', $value);
        $this->assertEquals($this->pointer->get('/foo/1'), $value);

        $this->pointer->set('/foo', $value);
        $this->assertEquals($this->pointer->get('/foo'), $value);

        $value = [1, 2, 3];
        $this->pointer->set('/foo', $value);
        $this->assertSame($this->pointer->get('/foo'), $value);
    }

    /**
     * @testdox a value can be removed
     * @dataProvider unsetPathProvider
     */
    public function testRemovePathValue($path)
    {
        $this->pointer->remove($path);
        try {
            $this->pointer->get($path);
        } catch (\gamringer\JSONPointer\Exception $e) {
            return;
        }

        $this->fail();
    }

    /**
     * @testdox trying to remove a non-existant path will return an exception
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testRemoveUnsetPathValue()
    {
        $this->pointer->remove('/bar');
    }

    /**
     * @testdox trying to remove an index in a numeric array
     */
    public function testRemoveArrayIndex()
    {
        $this->pointer->remove('/foo/0');
    }

    /**
     * @testdox root value can be unset
     */
    public function testUnsetRootValue()
    {
        $this->assertSame($this->pointer->getTarget(), $this->target);
        $this->pointer->remove('');
        $this->assertNull($this->target);
    }

    /**
     * @testdox root value can be replaced
     */
    public function testReplaceRootValue()
    {
        $newTarget = 'foo';


        $this->assertSame($this->pointer->getTarget(), $this->target);
        $this->pointer->set('', $newTarget);
        $this->assertSame($newTarget, $this->target);
    }

    /**
     * @testdox trying to remove an invalid path will throw an exception
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testInvalidUnsetPathValue()
    {
        $this->pointer->remove('foo');
    }

    /**
     * @testdox trying to get a non-existant path will return an exception
     * @dataProvider invalidPathProvider
     * @expectedException \gamringer\JSONPointer\Exception
     * @group test
     */
    public function testGetUnsetPathValue($path)
    {
        $this->pointer->get($path);
    }

    /**
     * @testdox Trying to retrieve null target should not return an exception
     */
    public function testGetNullValue()
    {
        $target = ['foo' => null];
        $pointer = new Pointer($target);

        $actual = $pointer->get('/foo');

        $this->assertNull($actual);
    }

    /**
     * @testdox trying to get a non-attainable path will return an exception
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetUnattainablePathValue()
    {
        $this->pointer->get('/foo/bar/0/1');
    }

    /**
     * @testdox trying to get an invalid path will throw an exception
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetInvalidPathValue()
    {
        $this->pointer->get('/q~ux');
    }

    /**
     * @testdox getting from an empty pointer will throw an exception
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetFromUnsetTarget()
    {
        (new Pointer())->get('');
    }

    /**
     * @testdox setting a new value will return the previous value
     */
    public function testReturnsPreviousValueOnSet()
    {
        $newValue = 'new value';
        $initialValue = $this->pointer->get('/foo');
        $previousValue = $this->pointer->set('/foo', $newValue);

        $this->assertSame($initialValue, $previousValue);
    }

    /**
     * @testdox removing a value will return the previous value
     */
    public function testReturnsPreviousValueOnRemove()
    {
        $initialValue = $this->pointer->get('/foo');
        $previousValue = $this->pointer->remove('/foo');

        $this->assertSame($initialValue, $previousValue);
    }

    public function pathProvider()
    {
        return [

            //  Regular JSON Paths
            [addslashes("")],
            [addslashes("/foo")],
            [addslashes("/foo/0")],
            [addslashes("/")],
            [addslashes("/a~1b")],
            [addslashes("/c%d")],
            [addslashes("/e^f")],
            [addslashes("/g|h")],
            [addslashes("/i\\j")],
            [addslashes("/k\"l")],
            [addslashes("/ ")],
            [addslashes("/m~0n")],
            [addslashes("/-")],

            //  URI Fragment Paths
            [addslashes('#')],
            [addslashes('#/foo')],
            [addslashes('#/foo/0')],
            [addslashes('#/')],
            [addslashes('#/a~1b')],
            [addslashes('#/c%25d')],
            [addslashes('#/e%5Ef')],
            [addslashes('#/g%7Ch')],
            [addslashes('#/i%5Cj')],
            [addslashes('#/k%22l')],
            [addslashes('#/%20')],
            [addslashes('#/m~0n')],
            [addslashes('#/-')],
        ];
    }

    public function invalidPathProvider()
    {
        return [
            ["qux"],
            ["/grault"],
            ["/q~ux"],
            ["/foo/2"],
            ["/foo/0/2"],
        ];
    }

    public function unsetPathProvider()
    {
        return [
            ["/qux"],
            ["/qux/quux"],
            ["/foo"],
        ];
    }
}

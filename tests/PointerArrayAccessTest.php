<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;

class PointerArrayAccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox getting value from Object as array
     */
    public function testGetPathValue()
    {
        $attributes = [
            'foo' => new ArrayAccessible(['bar' => 'str1', 'baz' => 'str2']),
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $this->assertEquals($attributes['foo']->bar, $pointer->get('/foo/bar'));
        $this->assertEquals($target->qux, $pointer->get('/qux'));
    }

    /**
     * @testdox setting value into Object as array
     */
    public function testSetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $this->assertEquals($attributes['qux'], $pointer->get('/qux'));
        $pointer->set('/qux', 'corge');
        $this->assertEquals('corge', $pointer->get('/qux'));
        $this->assertEquals('corge', $target->qux);
    }

    /**
     * @testdox unsetting value from Object as array
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testUnsetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $this->assertEquals($attributes['qux'], $pointer->get('/qux'));
        $pointer->remove('/qux');
        $pointer->get('/qux');
    }

    /**
     * @testdox getting an unset path
     *
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetUnsetPathValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $pointer->get('/corge');
    }

    /**
     * @testdox that root value can be unset
     */
    public function testUnsetRootValue()
    {
        $attributes = [
            'foo' => ['bar', 'baz'],
            'qux' => 'quux'
        ];
        $target = new ArrayAccessible($attributes);
        $pointer = new Pointer($target);

        $this->assertEquals($pointer->getTarget(), $target);
        $pointer->remove('');
        $this->assertEquals(null, $target);
    }

}

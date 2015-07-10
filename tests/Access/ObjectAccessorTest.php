<?php

namespace gamringer\JSONPointer\Access\Test;

use \gamringer\JSONPointer\Pointer;

class ObjectAccessorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->target = new \StdClass();
        $this->target->{"foo"} = ["bar", "baz"];
        $this->target->{"a/b"} = 1;
        $this->target->{"c%d"} = 2;
        $this->target->{"e^f"} = 3;
        $this->target->{"g|h"} = 4;
        $this->target->{"i\\j"} = 5;
        $this->target->{"k\"l"} = 6;
        $this->target->{" "} = 7;
        $this->target->{"m~n"} = 8;
        $this->target->{"-"} = 9;

        $this->pointer = new Pointer($this->target);
    }

    /**
     * @testdox Object target value is correctly set and retrieved
     */
    public function testGetSetTargetValue()
    {
        $value = 'qux';
        $this->assertNotEquals($this->pointer->get('/foo'), $value);
        $this->pointer->set('/foo', $value);
        $this->assertEquals($this->pointer->get('/foo'), $value);
    }

    /**
     * @testdox Object target value is correctly unset
     */
    public function testUnsetTargetValue()
    {
        $this->assertObjectHasAttribute('foo', $this->target);
        $this->pointer->remove('/foo');
        $this->assertObjectNotHasAttribute('foo', $this->target);
    }
}

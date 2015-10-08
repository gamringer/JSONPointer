<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;
use \gamringer\JSONPointer\Test\Resources\StdClassAccessor;

class PointerAccessorTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->target = new \stdClass();
        $this->target->foo = 'fooValue';
        $this->pointer = new Pointer($this->target);

    }

    /**
     * @testdox Can set a new Accessor for Object targets and retrieve Data
     * @group wip
     */
    public function testCanSetNew()
    {
        $this->pointer->getAccessorCollection()->setAccessor('\stdClass', new StdClassAccessor());

        $this->assertEquals($this->pointer->get('/foo'), $this->target->foo);

        $this->assertTrue(true);
    }
}

<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\Pointer;
use \gamringer\JSONPointer\Test\Resources\ArrayAccessible;

class PointerLastElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox setting the /- value does indeed create a new element
     */
    public function testCanSetNew()
    {
        $target = ['foo', 'bar', 'baz'];
        $pointer = new Pointer($target);

        $value = 'qux';

        $result = $pointer->set('/-', $value)->get('/3');

        $this->assertEquals($value, $result);

        return $pointer;
    }

    /**
     * @testdox the /- value should not be retrieveable in arrays
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testCannotGet()
    {
        $target = [];
        $pointer = new Pointer($target);

        $pointer->get('/-');
    }

    /**
     * @testdox the /- value should not be unsettable in arrays
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testCannotUnset()
    {
        $target = [];
        $pointer = new Pointer($target);

        $pointer->remove('/-');
    }

    /**
     * @testdox the /- value in a non-array is retrieveable
     */
    public function testCanGetLastElementTokenForNonArray()
    {
        $value = 'foo';
        $target = new ArrayAccessible(['-'=>$value]);
        $pointer = new Pointer($target);

        $result = $pointer->get('/-');

        $this->assertEquals($value, $result);
    }
}

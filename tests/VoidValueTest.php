<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\VoidValue;

class VoidValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox Can get values passed in constructor
     */
    public function testCanGet()
    {
        $owner = 'owner';
        $target = 'target';

        $value = new VoidValue($owner, $target);

        $this->assertSame($owner, $value->getOwner());
        $this->assertEquals($target, $value->getTarget());
    }
}

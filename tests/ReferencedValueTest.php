<?php

namespace gamringer\JSONPointer\Test;

use \gamringer\JSONPointer\ReferencedValue;
use \gamringer\JSONPointer\Access\ArrayAccessor;
use \gamringer\JSONPointer\Access\ObjectAccessor;

class ReferencedValueTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    /**
     * @testdox ReferencedValue can't construct when accessor doesn't cover it
     *
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testConstructorFails()
    {
        $owner = null;

        $stubAccessor = $this->getMockBuilder('\gamringer\JSONPointer\Access\Accesses')
                             ->getMock();

        $stubAccessor->method('covers')
                     ->willReturn(false);
        
        new ReferencedValue($owner, 'foo', $stubAccessor);
    }

    /**
     * @testdox Retrieve owner value
     *
     * @dataProvider getOwnerProvider
     */
    public function testGetOwner($owner)
    {
        $stubAccessor = $this->getMockBuilder('\gamringer\JSONPointer\Access\Accesses')
                             ->getMock();

        $stubAccessor->method('covers')
                     ->willReturn(true);
        
        $ref = new ReferencedValue($owner);
        $value = $ref->getValue();

        $this->assertSame($value, $owner);
    }

    public function getOwnerProvider()
    {
        return [
            ['allo'],
            [0],
            [1],
            ['0'],
            ['1'],
            [true],
            [false],
            [null],
            ['null'],
            ['true'],
            ['false'],
        ];
    }

    /**
     * @testdox Retrieve owner value
     *
     * @dataProvider getFromArrayProvider
     */
    public function testGetArrayIndex($owner, $index, $expected)
    {
        $accessor = new \gamringer\JSONPointer\Access\ArrayAccessor();
        
        $ref = new ReferencedValue($owner, $index, $accessor);
        $value = $ref->getValue();

        $this->assertEquals($value, $expected);
    }

    public function getFromArrayProvider()
    {
        return [
            [[0], 0, 0],
            [[1], 0, 1],
            [['0'], 0, '0'],
            [['1'], 0, '1'],
            [[true], 0, true],
            [[false], 0, false],
            [[null], 0, null],
            [['null'], 0, 'null'],
            [['true'], 0, 'true'],
            [['false'], 0, 'false'],
            [[null, 0, null], 1, 0],
            [[null, 1, null], 1, 1],
            [[null, '0', null], 1, '0'],
            [[null, '1', null], 1, '1'],
            [[null, true, null], 1, true],
            [[null, false, null], 1, false],
            [[null, null, null], 1, null],
            [[null, 'null', null], 1, 'null'],
            [[null, 'true', null], 1, 'true'],
            [[null, 'false', null], 1, 'false'],
            [[null, null, 0], 2, 0],
            [[null, null, 1], 2, 1],
            [[null, null, '0'], 2, '0'],
            [[null, null, '1'], 2, '1'],
            [[null, null, true], 2, true],
            [[null, null, false], 2, false],
            [[null, null, null], 2, null],
            [[null, null, 'null'], 2, 'null'],
            [[null, null, 'true'], 2, 'true'],
            [[null, null, 'false'], 2, 'false'],
        ];
    }

    /**
     * @testdox Retrieve owner last element value
     *
     * @expectedException \gamringer\JSONPointer\Exception
     */
    public function testGetArrayLastElementIndex()
    {
        $accessor = new \gamringer\JSONPointer\Access\ArrayAccessor();
        
        $owner = [];
        $ref = new ReferencedValue($owner, '-', $accessor);
        $value = $ref->getValue();

        $this->assertEquals($value, $expected);
    }

    /**
     * @testdox Set owner value
     *
     * @dataProvider getOwnerProvider
     */
    public function testSetOwner($owner)
    {
        $value = true;
        $ref = new ReferencedValue($owner);
        $ref->setValue($value);

        $this->assertSame($value, $owner);
    }

    /**
     * @testdox Set value
     */
    public function testSetObjectValue()
    {
        $owner = new \stdClass();
        $property = 'foo';

        $stubAccessor = $this->getMockBuilder('\gamringer\JSONPointer\Access\Accesses')
                             ->getMock();
        $stubAccessor->method('covers')
                     ->willReturn(true);
        $stubAccessor->expects($this->once())
                     ->method('setValue');

        $value = true;
        $ref = new ReferencedValue($owner, $property, $stubAccessor);
        $ref->setValue($value);
    }

    /**
     * @testdox Set next value
     * @group wip
     */
    public function testSetNextValue()
    {
        $owner = [1, 2];
        $property = '-';
        $place = sizeof($owner);

        $stubAccessor = $this->getMockBuilder('\gamringer\JSONPointer\Access\ArrayAccessor')
                             ->getMock();
        $stubAccessor->method('covers')
                     ->willReturn(true);
        $stubAccessor->method('isIndexedArray')
                     ->willReturn(true);

        $value = true;
        $ref = new ReferencedValue($owner, $property, $stubAccessor);
        $ref->setValue($value);

        $this->assertSame($value, $owner[$place]);
    }
}

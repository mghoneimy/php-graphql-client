<?php

namespace GraphQL\Tests;

require_once 'files_expected/input_objects/_TestFilterInputObject.php';

use GraphQL\Tests\SchemaObject\_TestFilterInputObject;
use PHPUnit\Framework\TestCase;

/**
 * Class InputObjectTest
 */
class InputObjectTest extends TestCase
{
    /**
     * @var _TestFilterInputObject
     */
    protected $inputObject;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->inputObject = new _TestFilterInputObject();
    }

    /**
     * @covers \GraphQL\SchemaObject\InputObject::__toString
     */
    public function testEmptyInputObject()
    {
        $this->assertEquals('{}', (string) $this->inputObject);
    }

    /**
     * @covers \GraphQL\SchemaObject\InputObject::toRawObject
     */
    public function testConvertToRawObject()
    {
        $this->assertEquals('{}', (string) $this->inputObject->toRawObject());
    }

    /**
     * @covers \GraphQL\SchemaObject\InputObject::toRawObject
     * @covers \GraphQL\SchemaObject\InputObject::__toString
     */
    public function testSetInputValues()
    {
        $this->inputObject->setFirstName('Mostafa');
        $this->assertEquals('{first_name: "Mostafa"}', (string) $this->inputObject);
        $this->assertEquals('{first_name: "Mostafa"}', (string) $this->inputObject->toRawObject());

        $this->inputObject->setLastName('Ghoneimy');
        $this->assertEquals('{first_name: "Mostafa", lastName: "Ghoneimy"}', (string) $this->inputObject);
        $this->assertEquals('{first_name: "Mostafa", lastName: "Ghoneimy"}', (string) $this->inputObject->toRawObject());

        $this->inputObject->setIds([2, 34, 567]);
        $this->assertEquals('{first_name: "Mostafa", lastName: "Ghoneimy", ids: [2, 34, 567]}', (string) $this->inputObject);
        $this->assertEquals('{first_name: "Mostafa", lastName: "Ghoneimy", ids: [2, 34, 567]}', (string) $this->inputObject->toRawObject());
    }
}
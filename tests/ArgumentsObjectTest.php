<?php

namespace GraphQL\Tests;

use GraphQL\SchemaObject\ArgumentsObject;
use GraphQL\SchemaObject\InputObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ArgumentsObjectTest
 *
 * @package GraphQL\Tests
 */
class ArgumentsObjectTest extends TestCase
{
    /**
     * @covers \GraphQL\SchemaObject\ArgumentsObject::toArray
     */
    public function testGetArrayOfEmptyObject()
    {
        $array = (new class extends ArgumentsObject {})->toArray();
        $this->assertIsArray($array);
        $this->assertEmpty($array);
    }

    /**
     * @covers \GraphQL\SchemaObject\ArgumentsObject::toArray
     */
    public function testGetArray()
    {
        $argumentsObj = (new SimpleArgumentsObject())->setStringProperty('string');
        $this->assertEquals(
            [
                'stringProperty'  => 'string',
            ],
            $argumentsObj->toArray()
        );

        $argumentsObj->setIntegerProperty(5);
        $this->assertEquals(
            [
                'stringProperty'  => 'string',
                'integerProperty' => 5,
            ],
            $argumentsObj->toArray()
        );

        $argumentsObj->setArrayProperty([1, 2, 3]);
        $this->assertEquals(
            [
                'stringProperty'  => 'string',
                'integerProperty' => 5,
                'arrayProperty'   => [1, 2, 3]
            ],
            $argumentsObj->toArray()
        );

        $filter = (new FilterInputObject())->setIds([1, 2]);
        $argumentsObj->setObjectProperty($filter);
        $this->assertEquals(
            [
                'stringProperty'  => 'string',
                'integerProperty' => 5,
                'arrayProperty'   => [1, 2, 3],
                'objectProperty'  => $filter
            ],
            $argumentsObj->toArray()
        );
    }
}

class SimpleArgumentsObject extends ArgumentsObject
{
    protected $stringProperty;
    protected $integerProperty;
    protected $arrayProperty;
    protected $objectProperty;

    public function setStringProperty(string $stringValue)
    {
        $this->stringProperty = $stringValue;
        return $this;
    }

    public function setIntegerProperty(int $integerValue)
    {
        $this->integerProperty = $integerValue;
        return $this;
    }

    public function setArrayProperty(array $arrayValue)
    {
        $this->arrayProperty = $arrayValue;
        return $this;
    }

    public function setObjectProperty(FilterInputObject $filterInputObject)
    {
        $this->objectProperty = $filterInputObject;
        return $this;
    }
}

class FilterInputObject extends InputObject
{
    protected $ids;

    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }
}
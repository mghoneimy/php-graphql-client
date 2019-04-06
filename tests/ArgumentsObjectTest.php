<?php

namespace GraphQL\Tests;

use GraphQL\SchemaObject\ArgumentsObject;
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
        $array = (new class extends ArgumentsObject {
            protected $stringProperty;
            protected $integerProperty;
            protected $arrayProperty;

            public function setStringProperty(string $stringValue) {
                $this->stringProperty = $stringValue;
                return $this;
            }

            public function setIntegerProperty(int $integerValue) {
                $this->integerProperty = $integerValue;
                return $this;
            }

            public function setArrayProperty(array $arrayValue) {
                $this->arrayProperty = $arrayValue;
                return $this;
            }
        })->setStringProperty('string')->setIntegerProperty(5)->setArrayProperty([1, 2, 3])->toArray();
        $this->assertIsArray($array);
        $this->assertEquals(
            [
                'stringProperty'  => 'string',
                'integerProperty' => 5,
                'arrayProperty'   => [1, 2, 3]
            ],
            $array
        );
    }
}
<?php

namespace GraphQL\Tests;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\SchemaObject\ArgumentsObject;
use GraphQL\SchemaObject\InputObject;
use GraphQL\SchemaObject\QueryObject;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryObjectTest
 *
 * @package GraphQL\Tests
 */
class QueryObjectTest extends TestCase
{
    /**
     * @var SimpleQueryObject
     */
    protected $queryObject;

    /**
     *
     */
    public function setUp(): void
    {
        $this->queryObject = new SimpleQueryObject('simples');
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::__construct
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     */
    public function testConstruct()
    {
        $object = new SimpleQueryObject();
        $object->selectScalar();
        $this->assertEquals(
            'query {
Simple {
scalar
}
}',
            $object->getQueryString());

        $object = new SimpleQueryObject('test');
        $object->selectScalar();
        $this->assertEquals(
            'query {
test {
scalar
}
}',
            $object->getQueryString());
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::__construct
     * @covers \GraphQL\Exception\EmptySelectionSetException
     */
    public function testEmptySelectionSet()
    {
        $this->expectException(EmptySelectionSetException::class);
        $this->queryObject->getQueryString();
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::selectField
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     */
    public function testSelectFields()
    {
        $this->queryObject->selectScalar();
        $this->assertEquals(
            'query {
simples {
scalar
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectAnotherScalar();
        $this->assertEquals(
            'query {
simples {
scalar
anotherScalar
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectSiblings()->selectScalar();
        $this->assertEquals(
            'query {
simples {
scalar
anotherScalar
siblings {
scalar
}
}
}',
            $this->queryObject->getQueryString()
        );
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::appendArguments
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     */
    public function testSelectSubFieldsWithArguments()
    {
        $this->queryObject->selectSiblings((new SimpleSiblingsArgumentObject())->setFirst(5)->setIds([1,2]))->selectScalar();
        $this->assertEquals(
            'query {
simples {
siblings(first: 5 ids: [1, 2]) {
scalar
}
}
}',
            $this->queryObject->getQueryString()
        );

        $this->setUp();
        $this->queryObject
            ->selectSiblings(
                (new SimpleSiblingsArgumentObject())
                    ->setObject(
                        (new class extends InputObject {
                            protected $field;

                            public function setField($field) {
                                $this->field = $field;
                                return $this;
                            }
                        })->setField('something')
                    )
            )
            ->selectScalar();
        $this->assertEquals(
            'query {
simples {
siblings(obj: {field: "something"}) {
scalar
}
}
}',
            $this->queryObject->getQueryString()
        );
    }
}

class SimpleQueryObject extends QueryObject
{
    const OBJECT_NAME = 'Simple';

    public function selectScalar()
    {
        $this->selectField('scalar');

        return $this;
    }

    public function selectAnotherScalar()
    {
        $this->selectField('anotherScalar');

        return $this;
    }

    public function selectSiblings(SimpleSiblingsArgumentObject $argumentObject = null)
    {
        $object = new SimpleQueryObject('siblings');
        if ($argumentObject !== null) {
            $object->appendArguments($argumentObject->toArray());
        }
        $this->selectField($object);

        return $object;
    }
}

class SimpleSiblingsArgumentObject extends ArgumentsObject
{
    protected $first;
    protected $ids;
    protected $obj;

    public function setFirst($first)
    {
        $this->first = $first;
        return $this;
    }

    public function setIds(array $ids)
    {
        $this->ids = $ids;
        return $this;
    }

    public function setObject($obj)
    {
        $this->obj = $obj;
        return $this;
    }
}

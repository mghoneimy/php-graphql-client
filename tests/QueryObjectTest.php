<?php

include 'files_expected/query_objects/OtherObjectQueryObject.php';
include 'files_expected/query_objects/TestQueryObject.php';

use GraphQL\SchemaObject\TestQueryObject;
use PHPUnit\Framework\TestCase;

class QueryObjectTest extends TestCase
{
    /**
     * @var TestQueryObject
     */
    protected $queryObject;

    public function setUp()
    {
        $this->queryObject = new TestQueryObject();
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     *
     * @throws \GraphQL\Exception\EmptySelectionSetException
     */
    public function testEmptySelectionSet()
    {
        $this->expectException(\GraphQL\Exception\EmptySelectionSetException::class);
        $this->queryObject->getQueryString();
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::selectField
     * @covers \GraphQL\SchemaObject\QueryObject::toQuery
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     *
     * @throws \GraphQL\Exception\EmptySelectionSetException
     */
    public function testSelectFields()
    {
        $this->queryObject->selectPropertyOne();
        $this->assertEquals(
            'query {
Test {
property_one
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectPropertyTwo();
        $this->assertEquals(
            'query {
Test {
property_one
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectOtherObjects()->selectName();
        $this->assertEquals(
            'query {
Test {
property_one
propertyTwo
other_objects {
name
}
}
}',
            $this->queryObject->getQueryString()
        );
    }

    /**
     * @covers \GraphQL\SchemaObject\QueryObject::constructArgumentsList
     * @covers \GraphQL\SchemaObject\QueryObject::toQuery
     * @covers \GraphQL\SchemaObject\QueryObject::getQueryString
     *
     * @throws \GraphQL\Exception\EmptySelectionSetException
     */
    public function testSetArguments()
    {
        $this->queryObject->selectPropertyTwo();
        $this->queryObject->setPropertyOne('value');
        $this->assertEquals(
            'query {
Test(property_one: "value") {
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->setPropertyTwo(true);
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true) {
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->setFirst(5);
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true first: 5) {
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->setPropertyTwos([1, 25, 87]);
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true first: 5 propertyTwos: [1, 25, 87]) {
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectOtherObjects()->selectName()->setFirst(2)->setOffset(10)->setName('some');
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true first: 5 propertyTwos: [1, 25, 87]) {
propertyTwo
other_objects(name: "some" first: 2 offset: 10) {
name
}
}
}',
            $this->queryObject->getQueryString()
        );
    }
}
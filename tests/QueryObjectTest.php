<?php

require_once 'files_expected/query_objects/OtherObjectQueryObject.php';
require_once 'files_expected/query_objects/TestQueryObject.php';
require_once 'files_expected/input_objects/_TestFilterInputObject.php';

use GraphQL\SchemaObject\_TestFilterInputObject;
use GraphQL\SchemaObject\TestQueryObject;
use PHPUnit\Framework\TestCase;

class QueryObjectTest extends TestCase
{
    /**
     * @var TestQueryObject
     */
    protected $queryObject;

    /**
     *
     */
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

        $this->queryObject->setPropertyTwos([1, 25, 87]);
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true propertyTwos: [1, 25, 87]) {
propertyTwo
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->selectOtherObjects()->selectName()->setName('some');
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true propertyTwos: [1, 25, 87]) {
propertyTwo
other_objects(name: "some") {
name
}
}
}',
            $this->queryObject->getQueryString()
        );

        $this->queryObject->setFilterBy((new _TestFilterInputObject())->setFirstName('Nameyy')->setIds([1, 5, 8]));
        $this->assertEquals(
            'query {
Test(property_one: "value" propertyTwo: true propertyTwos: [1, 25, 87] filterBy: {first_name: "Nameyy", ids: [1, 5, 8]}) {
propertyTwo
other_objects(name: "some") {
name
}
}
}',
            $this->queryObject->getQueryString()
        );
    }
}
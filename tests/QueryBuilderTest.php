<?php

namespace GraphQL\Tests;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\Query;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryBuilderTest
 *
 * @package GraphQL\Tests
 */
class QueryBuilderTest extends TestCase
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder('Object');
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::__construct
     */
    public function testConstruct()
    {
        $builder = new QueryBuilder('Object');
        $builder->selectField('field_one');
        $this->assertEquals(
            'query {
Object {
field_one
}
}',
            (string) $builder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     */
    public function testEmptySelectionSet()
    {
        $this->expectException(EmptySelectionSetException::class);
        $this->queryBuilder->getQuery();
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     */
    public function testSelectScalarFields()
    {
        $this->queryBuilder->selectField('field_one');
        $this->queryBuilder->selectField('field_two');
        $this->assertEquals(
            'query {
Object {
field_one
field_two
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     */
    public function testSelectNestedQuery()
    {
        $this->queryBuilder->selectField(
            (new Query('Nested'))
                ->setSelectionSet(['some_field'])
        );
        $this->assertEquals(
            'query {
Object {
Nested {
some_field
}
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    public function testSelectNestedQueryBuilder()
    {
        $this->queryBuilder->selectField(
            (new QueryBuilder('Nested'))
                ->selectField('some_field')
        );
        $this->assertEquals(
            'query {
Object {
Nested {
some_field
}
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::setArgument
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     */
    public function testSelectArguments()
    {
        $this->queryBuilder->selectField('field');
        $this->queryBuilder->setArgument('str_arg', 'value');
        $this->assertEquals(
            'query {
Object(str_arg: "value") {
field
}
}',
            (string) $this->queryBuilder->getQuery()
        );

        $this->queryBuilder->setArgument('bool_arg', true);
        $this->assertEquals(
            'query {
Object(str_arg: "value" bool_arg: true) {
field
}
}',
            (string) $this->queryBuilder->getQuery()
        );

        $this->queryBuilder->setArgument('int_arg', 10);
        $this->assertEquals(
            'query {
Object(str_arg: "value" bool_arg: true int_arg: 10) {
field
}
}',
            (string) $this->queryBuilder->getQuery()
        );

        $this->queryBuilder->setArgument('array_arg', ['one', 'two', 'three']);
        $this->assertEquals(
            'query {
Object(str_arg: "value" bool_arg: true int_arg: 10 array_arg: ["one", "two", "three"]) {
field
}
}',
            (string) $this->queryBuilder->getQuery()
        );

        $this->queryBuilder->setArgument('input_object_arg', new RawObject('{field_not: "x"}'));
        $this->assertEquals(
            'query {
Object(str_arg: "value" bool_arg: true int_arg: 10 array_arg: ["one", "two", "three"] input_object_arg: {field_not: "x"}) {
field
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::setArgument
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     */
    public function testSetTwoLevelArguments()
    {
        $this->queryBuilder->selectField(
            (new QueryBuilder('Nested'))
                ->selectField('some_field')
                ->selectField('another_field')
                ->setArgument('nested_arg', [1, 2, 3])
        )
        ->setArgument('outer_arg', 'outer val');
        $this->assertEquals(
            'query {
Object(outer_arg: "outer val") {
Nested(nested_arg: [1, 2, 3]) {
some_field
another_field
}
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }
}
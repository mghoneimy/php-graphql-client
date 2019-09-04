<?php

namespace GraphQL\Tests;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\InlineFragment;
use GraphQL\Query;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use GraphQL\Variable;
use PHPUnit\Framework\TestCase;

/**
 * This test case is responsible for testing the QueryBuilder and AbstractQueryBuilder classes
 *
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

    /**
     *
     */
    public function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder('Object');
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::__construct
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::__construct
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
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::getQuery
     * @covers \GraphQL\Exception\EmptySelectionSetException
     */
    public function testEmptySelectionSet()
    {
        $this->expectException(EmptySelectionSetException::class);
        $this->queryBuilder->getQuery();
    }


    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::__construct
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::__construct
     */
    public function testSelectFieldAlias()
    {
        $builder = new QueryBuilder('Object');
        $builder->selectField('field_one', 'fieldAlias');
        $this->assertEquals(
            'query {
Object {
fieldAlias:field_one
}
}',
            (string) $builder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::__construct
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::__construct
     */
    public function testSelectFieldAliasWithArguments()
    {
        $builder = new QueryBuilder('Object');
        $builder->selectField('field_one', 'fieldAlias', ['param1' => 'val1', 'param2' => 'val2' ]);
        $this->assertEquals(
            'query {
Object {
fieldAlias:field_one ( param1 : "val1"  param2 : "val2" )
}
}',
            (string) $builder->getQuery()
        );

    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::__construct
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::__construct
     */
    public function testSelectionSetAliasWithArguments()
    {
        $builder = new QueryBuilder('Object');

        $builder->selectField(
            (new Query('data'))->setArguments(['some_field' => 'params'])
                ->setSelectionSet(
                    [
                        'aliasId:first_field' => ['param1' => 'val1', 'param2' => 'val2' ],
                        'second_field' => ['param1' => 'val1', 'param2' => 'val2' ]
                    ])
        );

        $this->assertEquals(
            'query {
Object {
data(some_field: "params") {
   aliasId:first_field ( param1 : "val1"  param2 : "val2" )
   second_field ( param1 : "val1"  param2 : "val2" )
}
}
}',
            (string) $builder->getQuery()
        );


    }
    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::setVariable
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::setVariable
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::getQuery
     */
    public function testAddVariables()
    {
        $this->queryBuilder
            ->setVariable('var', 'String')
            ->setVariable('intVar', 'Int', false, 4)
            ->selectField('fieldOne');
        $this->assertEquals(
            'query($var: String $intVar: Int=4) {
Object {
fieldOne
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::getQuery
     */
    public function testAddVariablesToSecondLevelQueryDoesNothing()
    {
        $this->queryBuilder
            ->setVariable('var', 'String')
            ->selectField('fieldOne')
            ->selectField(
                (new QueryBuilder('Nested'))
                    ->setVariable('var', 'String')
                    ->selectField('fieldTwo')
            );
        $this->assertEquals(
            'query($var: String) {
Object {
fieldOne
Nested {
fieldTwo
}
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::selectField
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
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
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

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     */
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
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::selectField
     */
    public function testSelectInlineFragment()
    {
        $this->queryBuilder->selectField(
            (new InlineFragment('Type'))
                ->setSelectionSet(['field'])
        );
        $this->assertEquals(
            'query {
Object {
... on Type {
field
}
}
}',
            (string) $this->queryBuilder->getQuery()
        );
    }

    /**
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::setArgument
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::setArgument
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
     * @covers \GraphQL\QueryBuilder\QueryBuilder::getQuery
     * @covers \GraphQL\QueryBuilder\QueryBuilder::setArgument
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::setArgument
     * @covers \GraphQL\QueryBuilder\QueryBuilder::selectField
     * @covers \GraphQL\QueryBuilder\AbstractQueryBuilder::selectField
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
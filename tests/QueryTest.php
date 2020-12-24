<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\Exception\ArgumentException;
use GraphQL\Exception\InvalidSelectionException;
use GraphQL\Exception\InvalidVariableException;
use GraphQL\InlineFragment;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Variable;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryTest.
 *
 * @coversNothing
 */
class QueryTest extends TestCase
{
    /**
     * @covers \GraphQL\Query::__ToString
     * @covers \GraphQL\Query::__construct
     *
     * @return Query
     */
    public function testConvertsToString()
    {
        $query = new Query('Object');
        $this->assertIsString((string) $query, 'Failed to convert query to string');

        return $query;
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testEmptyArguments(Query $query)
    {
        $this->assertStringNotContainsString('()', (string) $query, 'Query has empty arguments list');

        return $query;
    }

    /**
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithoutFieldName()
    {
        $query = new Query();

        $this->assertSame(
            'query',
            (string) $query
        );

        $query->setSelectionSet(
            [
                (new Query('Object'))
                    ->setSelectionSet(['one']),
                (new Query('Another'))
                    ->setSelectionSet(['two']),
            ]
        );

        $this->assertSame(
            'query {
Object {
one
}
Another {
two
}
}',
            (string) $query
        );
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithAlias()
    {
        $query = (new Query('Object', 'ObjectAlias'))
            ->setSelectionSet([
                'one',
            ]);

        $this->assertSame(
            'query {
ObjectAlias: Object {
one
}
}',
            (string) $query
        );
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::setAlias
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithSetAlias()
    {
        $query = (new Query('Object'))
            ->setAlias('ObjectAlias')
            ->setSelectionSet([
                'one',
            ]);

        $this->assertSame(
            'query {
ObjectAlias: Object {
one
}
}',
            (string) $query
        );
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::setOperationName
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithOperationName()
    {
        $query = (new Query('Object'))
            ->setOperationName('retrieveObject');
        $this->assertSame(
            'query retrieveObject {
Object
}',
            (string) $query
        );
    }

    /**
     * @depends testQueryWithoutFieldName
     * @depends testQueryWithOperationName
     *
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::setOperationName
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithOperationNameAndOperationType()
    {
        $query = (new Query())
            ->setOperationName('retrieveObject')
            ->setSelectionSet([new Query('Object')]);
        $this->assertSame(
            'query retrieveObject {
Object
}',
            (string) $query
        );
    }

    /**
     * @depends testQueryWithOperationName
     *
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::setOperationName
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithOperationNameInSecondLevelDoesNothing()
    {
        $query = (new Query('Object'))
            ->setOperationName('retrieveObject')
            ->setSelectionSet([(new Query('Nested'))->setOperationName('opName')]);
        $this->assertSame(
            'query retrieveObject {
Object {
Nested
}
}',
            (string) $query
        );
    }

    /**
     * @covers \GraphQL\Query::setVariables
     * @covers \GraphQL\Exception\InvalidVariableException
     */
    public function testSetVariablesWithoutVariableObjects()
    {
        $this->expectException(InvalidVariableException::class);
        (new Query('Object'))->setVariables(['one', 'two']);
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::setVariables
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::constructVariables
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithOneVariable()
    {
        $query = (new Query('Object'))
            ->setVariables([new Variable('var', 'String')]);
        $this->assertSame(
            'query($var: String) {
Object
}',
            (string) $query
        );
    }

    /**
     * @depends testQueryWithOneVariable
     *
     * @covers \GraphQL\Query::setVariables
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::constructVariables
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithMultipleVariables()
    {
        $query = (new Query('Object'))
            ->setVariables([new Variable('var', 'String'), new Variable('intVar', 'Int', false, 4)]);
        $this->assertSame(
            'query($var: String $intVar: Int=4) {
Object
}',
            (string) $query
        );
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithVariablesInSecondLevelDoesNothing()
    {
        $query = (new Query('Object'))
            ->setVariables([new Variable('var', 'String'), new Variable('intVar', 'Int', false, 4)])
            ->setSelectionSet([(new Query('Nested'))])
            ->setVariables([new Variable('var', 'String'), new Variable('intVar', 'Int', false, 4)]);
        $this->assertSame(
            'query($var: String $intVar: Int=4) {
Object {
Nested
}
}',
            (string) $query
        );
    }

    /**
     * @depends testQueryWithMultipleVariables
     * @depends testQueryWithOperationName
     *
     * @covers \GraphQL\Query::generateSignature
     * @covers \GraphQL\Query::__toString
     */
    public function testQueryWithOperationNameAndVariables()
    {
        $query = (new Query('Object'))
            ->setOperationName('retrieveObject')
            ->setVariables([new Variable('var', 'String')]);
        $this->assertSame(
            'query retrieveObject($var: String) {
Object
}',
            (string) $query
        );
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::__toString
     *
     * @return Query
     */
    public function testEmptyQuery(Query $query)
    {
        $this->assertSame(
            'query {
Object
}',
            (string) $query,
            'Incorrect empty query string'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Exception\ArgumentException
     * @covers \GraphQL\Query::setArguments
     *
     * @return Query
     */
    public function testArgumentWithoutName(Query $query)
    {
        $this->expectException(ArgumentException::class);
        $query->setArguments(['val']);

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testStringArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => 'value']);
        $this->assertSame(
            'query {
Object(arg1: "value")
}',
            (string) $query,
            'Query has improperly formatted parameter list'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testIntegerArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => 23]);
        $this->assertSame(
            'query {
Object(arg1: 23)
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testBooleanArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => true]);
        $this->assertSame(
            'query {
Object(arg1: true)
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers  \GraphQL\Query::setArguments
     * @covers  \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testNullArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => null]);
        $this->assertSame(
            'query {
Object(arg1: null)
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testArrayIntegerArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => [1, 2, 3]]);
        $this->assertSame(
            'query {
Object(arg1: [1, 2, 3])
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers  \GraphQL\Query::setArguments
     * @covers  \GraphQL\Query::constructArguments
     * @covers  \GraphQL\RawObject::__toString
     *
     * @return Query
     */
    public function testJsonObjectArgumentValue(Query $query)
    {
        $query->setArguments(['obj' => new RawObject('{json_string_array: ["json value"]}')]);
        $this->assertSame(
            'query {
Object(obj: {json_string_array: ["json value"]})
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testArrayStringArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => ['one', 'two', 'three']]);
        $this->assertSame(
            'query {
Object(arg1: ["one", "two", "three"])
}',
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testStringArgumentValue
     * @depends testIntegerArgumentValue
     * @depends testBooleanArgumentValue
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     *
     * @return Query
     */
    public function testTwoOrMoreArguments(Query $query)
    {
        $query->setArguments(['arg1' => 'val1', 'arg2' => 2, 'arg3' => true]);
        $this->assertSame(
            'query {
Object(arg1: "val1" arg2: 2 arg3: true)
}',
            (string) $query,
            'Query has improperly formatted parameter list'
        );

        return $query;
    }

    /**
     * @depends testStringArgumentValue
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     */
    public function testStringWrappingWorks()
    {
        // TODO: Remove this in v1.0 release
        $queryWrapped = new Query('Object');
        $queryWrapped->setArguments(['arg1' => '"val"']);

        $queryNotWrapped = new Query('Object');
        $queryNotWrapped->setArguments(['arg1' => 'val']);

        $this->assertSame((string) $queryWrapped, (string) $queryWrapped);
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Query::setSelectionSet
     * @covers \GraphQL\FieldTrait::constructSelectionSet
     *
     * @return Query
     */
    public function testSingleSelectionField(Query $query)
    {
        $query->setSelectionSet(['field1']);
        $this->assertSame(
            'query {
Object {
field1
}
}',
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Query::setSelectionSet
     * @covers \GraphQL\FieldTrait::constructSelectionSet
     *
     * @return Query
     */
    public function testTwoOrMoreSelectionFields(Query $query)
    {
        $query->setSelectionSet(['field1', 'field2']);
        $this->assertSame(
            'query {
Object {
field1
field2
}
}',
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Exception\InvalidSelectionException
     * @covers \GraphQL\Query::setSelectionSet
     *
     * @return Query
     */
    public function testSelectNonStringValues(Query $query)
    {
        $this->expectException(InvalidSelectionException::class);
        $query->setSelectionSet([true, 1.5]);

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @coversNothing
     *
     * @return Query
     */
    public function testOneLevelQuery(Query $query)
    {
        $query->setSelectionSet(['field1', 'field2']);
        $query->setArguments(['arg1' => 'val1', 'arg2' => 'val2']);
        $this->assertSame(
            'query {
Object(arg1: "val1" arg2: "val2") {
field1
field2
}
}',
            (string) $query,
            'One level query not formatted correctly'
        );

        return $query;
    }

    /**
     * @depends clone testOneLevelQuery
     *
     * @covers \GraphQL\FieldTrait::constructSelectionSet
     * @covers \GraphQL\Query::setAsNested
     *
     * @return Query
     */
    public function testTwoLevelQueryDoesNotContainWordQuery(Query $query)
    {
        $query->setSelectionSet(
            [
                'field1',
                'field2',
                (new Query('Object2'))
                    ->setSelectionSet(['field3']),
            ]
        );
        $this->assertStringNotContainsString(
            "\nquery {",
            (string) $query,
            'Nested query contains "query" word'
        );

        return $query;
    }

    /**
     * @depends clone testTwoLevelQueryDoesNotContainWordQuery
     *
     * @covers \GraphQL\Query::setAsNested
     *
     * @return Query
     */
    public function testTwoLevelQuery(Query $query)
    {
        $query->setSelectionSet(
            [
                'field1',
                'field2',
                (new Query('Object2'))
                    ->setSelectionSet(['field3']),
            ]
        );
        $this->assertSame(
            'query {
Object(arg1: "val1" arg2: "val2") {
field1
field2
Object2 {
field3
}
}
}',
            (string) $query,
            'Two level query not formatted correctly'
        );

        return $query;
    }

    /**
     * @depends clone testTwoLevelQueryDoesNotContainWordQuery
     *
     * @return Query
     */
    public function testTwoLevelQueryWithInlineFragment(Query $query)
    {
        $query->setSelectionSet(
            [
                'field1',
                (new InlineFragment('Object'))
                    ->setSelectionSet(
                        [
                            'fragment_field1',
                            'fragment_field2',
                        ]
                    ),
            ]
        );
        $this->assertSame(
            'query {
Object(arg1: "val1" arg2: "val2") {
field1
... on Object {
fragment_field1
fragment_field2
}
}
}',
            (string) $query
        );

        return $query;
    }
}

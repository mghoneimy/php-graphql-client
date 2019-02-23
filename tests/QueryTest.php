<?php

use GraphQL\Exception\ArgumentException;
use GraphQL\Exception\InvalidSelectionException;
use GraphQL\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryTest
 */
class QueryTest extends TestCase
{
    /**
     * @covers \GraphQL\Query::__ToString
     *
     * @return Query
     */
    public function testConvertsToString()
    {
        $query = new Query('Object');
        $this->assertInternalType('string', (string) $query, 'Failed to convert query to string');

        return $query;
    }

    /**
     * @depends testConvertsToString
     *
     * @covers \GraphQL\Query::constructArguments
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testEmptyArguments(Query $query)
    {
        $this->assertNotContains("()", (string) $query, 'Query has empty arguments list');

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::__toString
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testEmptyQuery(Query $query)
    {
        $this->assertEquals(
            "query {
Object {

}
}",
            (string) $query,
            'Incorrect empty query string'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     *
     * @param Query $query
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
     * @covers \GraphQL\Util\StringLiteralFormatter::formatLiteralForGQLQuery
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testStringArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => 'value']);
        $this->assertEquals(
            "query {
Object(arg1: \"value\") {

}
}",
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
     * @covers \GraphQL\Util\StringLiteralFormatter::formatLiteralForGQLQuery
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testIntegerArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => 23]);
        $this->assertEquals(
            "query {
Object(arg1: 23) {

}
}",
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments

     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     * @covers \GraphQL\Util\StringLiteralFormatter::formatLiteralForGQLQuery
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testBooleanArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => true]);
        $this->assertEquals(
            "query {
Object(arg1: true) {

}
}",
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     * @covers \GraphQL\Util\StringLiteralFormatter::formatArrayForGQLQuery
     *
     * @param  Query $query
     *
     * @return Query
     */
    public function testArrayIntegerArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => [1, 2, 3]]);
        $this->assertEquals(
            "query {
Object(arg1: [1, 2, 3]) {

}
}",
            (string) $query
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @covers \GraphQL\Query::setArguments
     * @covers \GraphQL\Query::constructArguments
     * @covers \GraphQL\Util\StringLiteralFormatter::formatArrayForGQLQuery
     *
     * @param  Query $query
     *
     * @return Query
     */
    public function testArrayStringArgumentValue(Query $query)
    {
        $query->setArguments(['arg1' => ['one', 'two', 'three']]);
        $this->assertEquals(
            "query {
Object(arg1: [\"one\", \"two\", \"three\"]) {

}
}",
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
     * @param Query $query
     *
     * @return Query
     */
    public function testTwoOrMoreArguments(Query $query)
    {
        $query->setArguments(['arg1' => 'val1', 'arg2' => 2, 'arg3' => true]);
        $this->assertEquals(
            "query {
Object(arg1: \"val1\" arg2: 2 arg3: true) {

}
}",
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
        $queryWrapped = new Query('Object');
        $queryWrapped->setArguments(['arg1' => '"val"']);

        $queryNotWrapped = new Query('Object');
        $queryNotWrapped->setArguments(['arg1' => 'val']);

        $this->assertEquals((string) $queryWrapped, (string) $queryWrapped);
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Query::setSelectionSet
     * @covers \GraphQL\Query::constructSelectionSet
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testSingleSelectionField(Query $query)
    {
        $query->setSelectionSet(['field1']);
        $this->assertEquals(
            "query {
Object {
field1
}
}",
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Query::setSelectionSet
     * @covers \GraphQL\Query::constructSelectionSet
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testTwoOrMoreSelectionFields(Query $query)
    {
        $query->setSelectionSet(['field1', 'field2']);
        $this->assertEquals(
            "query {
Object {
field1
field2
}
}",
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @covers \GraphQL\Query::setSelectionSet
     *
     * @param Query $query
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
     * @param Query $query
     *
     * @return Query
     */
    public function testOneLevelQuery(Query $query)
    {
        $query->setSelectionSet(['field1', 'field2']);
        $query->setArguments(['arg1' => 'val1', 'arg2' => 'val2']);
        $this->assertEquals(
            "query {
Object(arg1: \"val1\" arg2: \"val2\") {
field1
field2
}
}",
            (string) $query,
            'One level query not formatted correctly'
        );

        return $query;
    }

    /**
     * @depends clone testOneLevelQuery
     *
     * @covers \GraphQL\Query::constructSelectionSet
     *
     * @param Query $query
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
                    ->setSelectionSet(['field3'])
            ]
        );
        $this->assertNotContains(
            "\nquery {",
            (string) $query,
            'Nested query contains "query" word'
        );

        return $query;
    }

    /**
     * @depends clone testTwoLevelQueryDoesNotContainWordQuery
     *
     * @coversNothing
     *
     * @param Query $query
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
                    ->setSelectionSet(['field3'])
            ]
        );
        $this->assertEquals(
            "query {
Object(arg1: \"val1\" arg2: \"val2\") {
field1
field2
Object2 {
field3
}
}
}",
            (string) $query,
            'Two level query not formatted correctly'
        );

        return $query;
    }
}
<?php

use GraphQL\Exception\ArgumentException;
use GraphQL\Query;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/27/19
 * Time: 11:11 PM
 */

class QueryTest extends TestCase
{
    /**
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
     * @param Query $query
     *
     * @return Query
     */
    public function testEmptyQuery(Query $query)
    {
        $this->assertEquals(
            "query {\nObject {\n\n}\n}",
            (string) $query,
            'Incorrect empty query string'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
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
     * @param Query $query
     *
     * @return Query
     */
    public function testOneArgument(Query $query)
    {
        $query->setArguments(['arg1' => 'value']);
        $this->assertEquals(
            "query {\nObject(arg1: \"value\") {\n\n}\n}",
            (string) $query,
            'Query has improperly formatted parameter list'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyArguments
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testTwoOrMoreArguments(Query $query)
    {
        $query->setArguments(['arg1' => 'val1', 'arg2' => 'val2']);
        $this->assertEquals(
            "query {\nObject(arg1: \"val1\" arg2: \"val2\") {\n\n}\n}",
            (string) $query,
            'Query has improperly formatted parameter list'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testSingleSelectionField(Query $query)
    {
        $query->setSelectionSet(['field1']);
        $this->assertEquals(
            "query {\nObject {\nfield1\n}\n}",
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
     *
     * @param Query $query
     *
     * @return Query
     */
    public function testTwoOrMoreSelectionFields(Query $query)
    {
        $query->setSelectionSet(['field1', 'field2']);
        $this->assertEquals(
            "query {\nObject {\nfield1\nfield2\n}\n}",
            (string) $query,
            'Query has improperly formatted selection set'
        );

        return $query;
    }

    /**
     * @depends clone testEmptyQuery
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
            "query {\nObject(arg1: \"val1\" arg2: \"val2\") {\nfield1\nfield2\n}\n}",
            (string) $query,
            'One level query not formatted correctly'
        );

        return $query;
    }

    /**
     * @depends clone testOneLevelQuery
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
            "query {\nObject(arg1: \"val1\" arg2: \"val2\") {\nfield1\nfield2\nObject2 {\nfield3\n}\n}\n}",
            (string) $query,
            'Two level query not formatted correctly'
        );

        return $query;
    }
}
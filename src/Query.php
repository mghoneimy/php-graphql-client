<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/4/18
 * Time: 11:56 PM
 */

namespace GraphQL;

use GraphQL\Exception\ArgumentException;
use GraphQL\Exception\InvalidSelectionException;
use GraphQL\Util\StringLiteralFormatter;

/**
 * Class Query
 *
 * @package GraphQL
 */
class Query
{
    /**
     * Stores the GraphQL query format
     *
     * @var string
     */
    private static $queryFormat = "%s%s {\n%s\n}";

    /**
     * Stores the object being queried for
     *
     * @var string
     */
    private $object;

    /**
     * Stores the list of arguments used when querying data
     *
     * @var array
     */
    private $arguments;

    /**
     * Stores the selection set desired to get from the query, can include nested queries
     *
     * @var array
     */
    private $selectionSet;

    /**
     * Private member that's not accessible from outside the class, used internally to deduce if query is nested or not
     *
     * @var bool
     */
    private $isNested;

    /**
     * GQLQueryBuilder constructor.
     *
     * @param string $queryObject
     */
    public function __construct($queryObject)
    {
        $this->object       = $queryObject;
        $this->arguments    = [];
        $this->selectionSet = [];
        $this->isNested     = false;
    }

    /**
     * Throwing exception when setting the arguments if they are incorrect because we can't throw an exception during
     * the execution of __ToString(), it's a fatal error in PHP
     *
     * @param array $arguments
     *
     * @return Query
     * @throws ArgumentException
     */
    public function setArguments(array $arguments)
    {
        // If one of the arguments does not have a name provided, throw an exception
        $nonStringArgs = array_filter(array_keys($arguments), function($element) {
            return !is_string($element);
        });
        if (!empty($nonStringArgs)) {
            throw new ArgumentException('One or more of the arguments provided for creating the query does not have a name');
        }

        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $selectionSet
     *
     * @return Query
     * @throws InvalidSelectionException
     */
    public function setSelectionSet(array $selectionSet)
    {
        $nonStringsFields = array_filter($selectionSet, function($element) {
            return !is_string($element) && !$element instanceof Query;
        });
        if (!empty($nonStringsFields)) {
            throw new InvalidSelectionException('One or more of the selection fields provided is not of type string ro Query');
        }

        $this->selectionSet = $selectionSet;

        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function constructArguments()
    {
        // Return empty string if list is empty
        if (empty($this->arguments)) {
            return '';
        }

        // Construct arguments string if list not empty
        $constraintsString = '(';
        $first             = true;
        foreach ($this->arguments as $name => $value) {

            // Append space at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $constraintsString .= ' ';
            }

            // Convert argument values to graphql string literal equivalent
            if (is_scalar($value)) {
                // Convert scalar value to its literal in graphql
                $value = StringLiteralFormatter::formatLiteralForGQLQuery($value);
            } elseif (is_array($value)) {
                // Convert PHP array to its array representation in graphql arguments
                $value = StringLiteralFormatter::formatArrayForGQLQuery($value);
            }
            $constraintsString .= $name . ': ' . $value;
        }
        $constraintsString .= ')';

        return $constraintsString;
    }

    /**
     * @return string
     */
    protected function constructSelectionSet()
    {
        $attributesString = '';
        $first            = true;
        foreach ($this->selectionSet as $attribute) {

            // Append empty line at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $attributesString .= "\n";
            }

            // If query is included in attributes set as a nested query
            if ($attribute instanceof Query) {
                $attribute->setAsNested();
            }

            // Append attribute to returned attributes list
            $attributesString .= $attribute;
        }

        return $attributesString;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        $queryFormat = static::$queryFormat;
        if (!$this->isNested) {
            $queryFormat = "query {\n" . static::$queryFormat . "\n}";
        }
        $argumentsString    = $this->constructArguments();
        $selectionSetString = $this->constructSelectionSet();

        return sprintf($queryFormat, $this->object, $argumentsString, $selectionSetString);
    }

    /**
     *
     */
    private function setAsNested()
    {
        $this->isNested = true;
    }
}

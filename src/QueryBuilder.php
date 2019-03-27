<?php

namespace GraphQL;

use GraphQL\Exception\EmptySelectionSetException;

/**
 * Class QueryBuilder
 *
 * @package GraphQL
 */
class QueryBuilder
{
    /**
     * @var string
     */
    protected $queryObject;

    /**
     * @var array
     */
    protected $selectionSet;

    /**
     * @var array
     */
    protected $argumentsList;

    /**
     * QueryBuilder constructor.
     *
     * @param string $queryObject
     */
    public function __construct(string $queryObject)
    {
        $this->queryObject   = $queryObject;
        $this->selectionSet  = [];
        $this->argumentsList = [];
    }

    /**
     * @param string|QueryBuilder|Query $selectedField
     *
     * @return $this
     */
    public function selectField($selectedField)
    {
        if (is_string($selectedField) || $selectedField instanceof QueryBuilder || $selectedField instanceof Query) {
            $this->selectionSet[] = $selectedField;
        }

        return $this;
    }

    /**
     * @param $argumentName
     * @param $argumentValue
     *
     * @return $this
     */
    public function setArgument(string $argumentName, $argumentValue)
    {
        if (is_scalar($argumentValue) || is_array($argumentValue) || $argumentValue instanceof RawObject) {
            $this->argumentsList[$argumentName] = $argumentValue;
        }

        return $this;
    }

    /**
     * @return Query
     */
    public function toQuery(): Query
    {
        if (empty($this->selectionSet)) {
            throw new EmptySelectionSetException(static::class);
        }

        // Convert nested query builders to query objects
        foreach ($this->selectionSet as $key => $field) {
            if ($field instanceof QueryBuilder) {
                $this->selectionSet[$key] = $field->toQuery();
            }
        }

        $query = new Query($this->queryObject);
        $query->setArguments($this->argumentsList);
        $query->setSelectionSet($this->selectionSet);

        return $query;
    }
}
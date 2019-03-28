<?php

namespace GraphQL;

/**
 * Class QueryBuilder
 *
 * @package GraphQL
 */
class QueryBuilder extends AbstractQueryBuilder
{
    /**
     * Changing method visibility to public
     *
     * @param Query|QueryBuilder|string $selectedField
     *
     * @return AbstractQueryBuilder|QueryBuilder
     */
    public function selectField($selectedField)
    {
        return parent::selectField($selectedField);
    }

    /**
     * Changing method visibility to public
     *
     * @param string $argumentName
     * @param        $argumentValue
     *
     * @return AbstractQueryBuilder
     */
    public function setArgument(string $argumentName, $argumentValue)
    {
        return parent::setArgument($argumentName, $argumentValue);
    }

    /**
     * Changing method visibility to public
     *
     * @return Query
     */
    public function toQuery(): Query
    {
        return parent::toQuery();
    }

    /**
     * @return array
     */
    protected function constructArgumentsList(): array
    {
        return [];
    }
}
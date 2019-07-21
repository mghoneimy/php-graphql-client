<?php

namespace GraphQL\QueryBuilder;

use GraphQL\Query;
use GraphQL\Variable;

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
     * @return AbstractQueryBuilder|QueryBuilder
     */
    public function setArgument(string $argumentName, $argumentValue)
    {
        return parent::setArgument($argumentName, $argumentValue);
    }

    /**
     * @param Variable $variable
     *
     * @return AbstractQueryBuilder
     */
    public function setVariable(Variable $variable)
    {
        return parent::setVariable($variable);
    }
}
<?php

namespace GraphQL\QueryBuilder;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\InlineFragment;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Variable;

/**
 * Class AbstractQueryBuilder
 *
 * @package GraphQL
 */
abstract class AbstractQueryBuilder implements QueryBuilderInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var array|Variable[]
     */
    private $variables;

    /**
     * @var array
     */
    private $selectionSet;

    /**
     * @var array
     */
    private $argumentsList;

    /**
     * QueryBuilder constructor.
     *
     * @param string $queryObject
     */
    public function __construct(string $queryObject)
    {
        $this->query         = new Query($queryObject);
        $this->variables     = [];
        $this->selectionSet  = [];
        $this->argumentsList = [];
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        if (empty($this->selectionSet)) {
            throw new EmptySelectionSetException(static::class);
        }

        // Convert nested query builders to query objects
        foreach ($this->selectionSet as $key => $field) {
            if ($field instanceof AbstractQueryBuilder) {
                $this->selectionSet[$key] = $field->getQuery();
            }
        }

        $this->query->setVariables($this->variables);
        $this->query->setArguments($this->argumentsList);
        $this->query->setSelectionSet($this->selectionSet);

        return $this->query;
    }

    /**
     * @param string|QueryBuilder|Query $selectedField
     *
     * @param string|null $alias
     * @param array $arguments
     * @return $this
     */
    protected function selectField($selectedField, string $alias = null, array $arguments = [])
    {
        if (
            is_string($selectedField)
            || $selectedField instanceof AbstractQueryBuilder
            || $selectedField instanceof Query
            || $selectedField instanceof InlineFragment
        ) {

            // Set an alias for selectedField
            $selectedField = ($alias) ? $alias .':'. $selectedField : $selectedField;

            // Add arguments for selectedField
            if (is_array($arguments) && count($arguments)) {

                $params = '';
                foreach ($arguments as $k => $v) {
                    $params.= " $k : \"$v\" ";
                }
                $selectedField .= " ($params)";
            }


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
    protected function setArgument(string $argumentName, $argumentValue)
    {
        if (is_scalar($argumentValue) || is_array($argumentValue) || $argumentValue instanceof RawObject) {
            $this->argumentsList[$argumentName] = $argumentValue;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool   $isRequired
     * @param null   $defaultValue
     *
     * @return $this
     */
    protected function setVariable(string $name, string $type, bool $isRequired = false, $defaultValue = null)
    {
        $this->variables[] = new Variable($name, $type, $isRequired, $defaultValue);

        return $this;
    }
}
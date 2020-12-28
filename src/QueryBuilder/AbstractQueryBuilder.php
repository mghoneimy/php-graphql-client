<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\QueryBuilder;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\InlineFragment;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Variable;

/**
 * Class AbstractQueryBuilder.
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
     */
    public function __construct(string $queryObject = '', string $alias = '')
    {
        $this->query = new Query($queryObject, $alias);
        $this->variables = [];
        $this->selectionSet = [];
        $this->argumentsList = [];
    }

    /**
     * @return $this
     */
    public function setAlias(string $alias)
    {
        $this->query->setAlias($alias);

        return $this;
    }

    public function getQuery(): Query
    {
        if (empty($this->selectionSet)) {
            throw new EmptySelectionSetException(static::class);
        }

        // Convert nested query builders to query objects
        foreach ($this->selectionSet as $key => $field) {
            if ($field instanceof self) {
                $this->selectionSet[$key] = $field->getQuery();
            }
        }

        $this->query->setVariables($this->variables);
        $this->query->setArguments($this->argumentsList);
        $this->query->setSelectionSet($this->selectionSet);

        return $this->query;
    }

    /**
     * @param Query|QueryBuilder|string $selectedField
     *
     * @return $this
     */
    protected function selectField($selectedField)
    {
        if (
            \is_string($selectedField)
            || $selectedField instanceof self
            || $selectedField instanceof Query
            || $selectedField instanceof InlineFragment
        ) {
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
        if (is_scalar($argumentValue) || \is_array($argumentValue) || $argumentValue instanceof RawObject) {
            $this->argumentsList[$argumentName] = $argumentValue;
        }

        return $this;
    }

    /**
     * @param null $defaultValue
     *
     * @return $this
     */
    protected function setVariable(string $name, string $type, bool $isRequired = false, $defaultValue = null)
    {
        $this->variables[] = new Variable($name, $type, $isRequired, $defaultValue);

        return $this;
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\QueryBuilder;

use GraphQL\Query;

/**
 * Class QueryBuilder.
 */
class QueryBuilder extends AbstractQueryBuilder
{
    /**
     * Changing method visibility to public.
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
     * Changing method visibility to public.
     *
     * @param $argumentValue
     *
     * @return AbstractQueryBuilder|QueryBuilder
     */
    public function setArgument(string $argumentName, $argumentValue)
    {
        return parent::setArgument($argumentName, $argumentValue);
    }

    /**
     * Changing method visibility to public.
     *
     * @param null $defaultValue
     *
     * @return AbstractQueryBuilder|QueryBuilder
     */
    public function setVariable(string $name, string $type, bool $isRequired = false, $defaultValue = null)
    {
        return parent::setVariable($name, $type, $isRequired, $defaultValue);
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

/**
 * Class Mutation.
 */
class Mutation extends Query
{
    /**
     * Stores the name of the type of the operation to be executed on the GraphQL server.
     *
     * @var string
     */
    protected const OPERATION_TYPE = 'mutation';

    protected function constructSelectionSet(): string
    {
        if (empty($this->selectionSet)) {
            return '';
        }

        return parent::constructSelectionSet();
    }
}

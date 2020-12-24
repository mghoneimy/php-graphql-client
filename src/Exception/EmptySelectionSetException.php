<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Exception;

use UnderflowException;

/**
 * Class EmptySelectionSetException.
 */
class EmptySelectionSetException extends UnderflowException
{
    public function __construct($objectName)
    {
        parent::__construct("Query object of type \"${objectName}\" has an empty selection set");
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

/**
 * Class NestableObject.
 *
 * @codeCoverageIgnore
 */
abstract class NestableObject
{
    // TODO: Remove this method and class entirely, it's purely tech debt

    /**
     * @return mixed
     */
    abstract protected function setAsNested();
}

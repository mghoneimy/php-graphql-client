<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

/**
 * Class RawObject.
 */
class RawObject
{
    /**
     * @var string
     */
    protected $objectString;

    /**
     * JsonObject constructor.
     */
    public function __construct(string $objectString)
    {
        $this->objectString = $objectString;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->objectString;
    }
}

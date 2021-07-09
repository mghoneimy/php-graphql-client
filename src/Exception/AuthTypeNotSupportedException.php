<?php

namespace GraphQL\Exception;

use RunTimeException;

/**
 * Class AuthTypeNotSupportedException
 *
 * @package GraphQL\Exception
 */
class AuthTypeNotSupportedException extends RunTimeException
{
    public function __construct($authType)
    {
        parent::__construct("Type \"$authType\" is currently unsupported by client.");
    }
}

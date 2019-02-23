<?php

namespace GraphQL\Exception;

/**
 * Class InvalidSelectionException
 *
 * @package GraphQL\Exception
 */
class InvalidSelectionException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
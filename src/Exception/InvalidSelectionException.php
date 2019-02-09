<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/10/19
 * Time: 12:31 AM
 */

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
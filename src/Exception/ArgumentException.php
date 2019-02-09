<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/28/19
 * Time: 12:52 AM
 */

namespace GraphQL\Exception;

use Throwable;

/**
 * Class ArgumentException
 *
 * @package GraphQL\Exception
 */
class ArgumentException extends \Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
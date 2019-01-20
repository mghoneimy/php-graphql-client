<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/20/19
 * Time: 1:46 AM
 */

namespace GraphQL\Exception;

/**
 * Class EmptySelectionSetException
 *
 * @package GraphQL\Exception
 */
class EmptySelectionSetException extends QueryObjectException
{
    public function __construct($objectName)
    {
        parent::__construct("Query object of type \"$objectName\" has an empty selection set");
    }
}
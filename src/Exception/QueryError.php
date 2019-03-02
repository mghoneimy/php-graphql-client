<?php

namespace GraphQL\Exception;

/**
 * This exception is triggered when the GraphQL endpoint returns an error in the provided query
 *
 * Class QueryError
 *
 * @package GraphQl\Exception
 */
class QueryError extends \Exception
{
    /**
     * @var array
     */
    protected $errorDetails;

    /**
     * QueryError constructor.
     *
     * @param array $errorDetails
     */
    public function __construct($errorDetails)
    {
        $this->errorDetails = $errorDetails;
        parent::__construct($errorDetails['message']);
    }

    /**
     * @return array
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }
}
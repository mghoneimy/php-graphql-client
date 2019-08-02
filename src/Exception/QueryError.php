<?php

namespace GraphQL\Exception;

use RuntimeException;

/**
 * This exception is triggered when the GraphQL endpoint returns an error in the provided query
 *
 * Class QueryError
 *
 * @package GraphQl\Exception
 */
class QueryError extends RuntimeException
{
    /**
     * @var array
     */
    protected $errorDetails;

    /**
     * @var array
     */
    protected $data;

    /**
     * QueryError constructor.
     *
     * @param array $errorDetails
     */
    public function __construct($errorDetails)
    {
        $this->errorDetails = $errorDetails['errors'][0];
        $this->data         = $errorDetails['data'];
        parent::__construct($this->errorDetails['message']);
    }

    /**
     * @return array
     */
    public function getErrorDetails(): array
    {
        return $this->errorDetails;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
<?php

namespace GraphQL\Exception;

use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
     * @var RequestInterface
     */
    protected $requestObject;

    /**
     * @var ResponseInterface
     */
    protected $responseObject;

    /**
     * QueryError constructor.
     *
     * @param array $errorDetails
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct($errorDetails, $request, $response)
    {
        $this->errorDetails = $errorDetails['errors'][0];
        $this->requestObject = $request;
        $this->responseObject = $response;
        parent::__construct($this->errorDetails['message']);
    }

    /**
     * @return array
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }

    /**
     * @return RequestInterface
     */
    public function getRequestObject()
    {
        return $this->requestObject;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}
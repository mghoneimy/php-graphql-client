<?php

namespace GraphQL\Exception\Client;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Exception when an HTTP error occurs (4xx or 5xx error)
 *
 * Class ConnectException
 * @package GraphQL\Exception\Request
 */
class ConnectException extends RequestException implements NetworkExceptionInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_ERROR_MESSAGE = 'Network error';

    /**
     * ConnectException constructor.
     * @param string            $message
     * @param RequestInterface  $request
     * @param ResponseInterface|null $response
     * @param Throwable|null    $previous
     */
    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        parent::__construct($message, $request, $response, $previous);
    }

    /**
     * This function narrows the return type from the parent class and does not allow it to be nullable.
     */
    public function getRequest(): RequestInterface
    {
        /** @var RequestInterface */
        return parent::getRequest();
    }
}

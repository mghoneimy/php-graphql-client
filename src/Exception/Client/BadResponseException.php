<?php

namespace GraphQL\Exception\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Exception when an HTTP error occurs (4xx or 5xx error)
 *
 * Class BadResponseException
 * @package GraphQL\Exception
 */
class BadResponseException extends RequestException
{
    /**
     * BadResponseException constructor.
     * @param string            $message
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param Throwable|null    $previous
     */
    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response,
        Throwable $previous = null
    ) {
        parent::__construct($message, $request, $response, $previous);
    }

    /**
     * Current exception and the ones that extend it will always have a response.
     */
    public function hasResponse(): bool
    {
        return true;
    }

    /**
     * This function narrows the return type from the parent class and does not allow it to be nullable.
     */
    public function getResponse(): ResponseInterface
    {
        /** @var ResponseInterface */
        return parent::getResponse();
    }
}

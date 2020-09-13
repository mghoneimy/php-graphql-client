<?php

namespace GraphQL\Tests\Helper;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Helper class for making requests
 *
 * Class MockRequest
 * @package GraphQL\Tests
 */
class MockRequest
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var StreamInterface
     */
    private $stream;


    /**
     * MockRequest constructor.
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param StreamInterface   $stream
     */
    public function __construct(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $this->request = $request;
        $this->response = $response;
        $this->stream = $stream;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }
}

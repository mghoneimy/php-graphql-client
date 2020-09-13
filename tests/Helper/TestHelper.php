<?php

namespace GraphQL\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class TestHelper
 * @package GraphQL\Tests
 */
class TestHelper extends TestCase
{
    /**
     * @param string $uri
     * @param string $method
     * @param string $body
     * @param int    $responseCode
     * @return MockRequest
     */
    public function mockRequest(
        string $uri = '',
        string $method = 'GET',
        string $body = '',
        int $responseCode = 200
    ): MockRequest
    {
        $request = $this->createMockRequest($method, $uri);
        $stream = $this->createMockStream($body);
        $response = $this->createMockResponse($stream, $responseCode);

        return new MockRequest($request, $response, $stream);
    }

    /**
     * @param string $body
     * @return MockObject|StreamInterface
     */
    public function createMockStream(string $body = ''): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        return $stream;
    }

    /**
     * @param string|StreamInterface $body
     * @param int    $responseCode
     * @return MockObject|ResponseInterface
     */
    public function createMockResponse($body = '', int $responseCode = 200): ResponseInterface
    {
        $stream = $body instanceof StreamInterface
            ? $body
            : $this->createMockStream($body);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($responseCode);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    /**
     * @param string $method
     * @param string $uri
     * @return MockObject|RequestInterface
     */
    public function createMockRequest(string $method = 'POST', string $uri = ''): RequestInterface
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('withBody')->willReturnSelf();
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);

        return $request;
    }

    /**
     * @return MockObject|ClientExceptionInterface
     */
    public function createMockClientException(): ClientExceptionInterface
    {
        return $this->createMock(ClientExceptionInterface::class);
    }

    /**
     * @param RequestInterface|null $request
     * @return RequestExceptionInterface
     */
    public function createMockRequestException(RequestInterface $request = null): RequestExceptionInterface
    {
        $exception = $this->createMock(RequestExceptionInterface::class);
        $exception->method('getRequest')->willReturn($request ?? $this->createMockRequest());

        return $exception;
    }

    /**
     * @param RequestInterface|null $request
     * @return NetworkExceptionInterface
     */
    public function createMockNetworkException(RequestInterface $request = null): NetworkExceptionInterface
    {
        $exception = $this->createMock(NetworkExceptionInterface::class);
        $exception->method('getRequest')->willReturn($request ?? $this->createMockRequest());

        return $exception;
    }
}

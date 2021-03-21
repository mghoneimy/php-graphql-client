<?php

namespace GraphQL\Tests\Helper;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class TestHelper
 * @package GraphQL\Tests
 */
class TestHelper extends TestCase
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @return MockObject|RequestFactoryInterface
     */
    public function getRequestFactory(): RequestFactoryInterface
    {
        if (isset($this->requestFactory)) {
            return $this->requestFactory;
        }

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturnCallback([$this, 'createMockRequest']);
        return $this->requestFactory = $requestFactory;
    }

    /**
     * @return StreamFactoryInterface
     */
    public function getStreamFactory(): StreamFactoryInterface
    {
        if (isset($this->streamFactory)) {
            return $this->streamFactory;
        }

        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        $streamFactory->method('createStream')->willReturnCallback([$this, 'createMockStreamFromFactoryFromString']);
        $streamFactory->method('createStreamFromFile')->willReturnCallback([$this, 'createMockStreamFromFactoryFromFile']);
        $streamFactory->method('createStreamFromResource')->willReturnCallback([$this, 'createMockStreamFromFactoryFromResource']);

        return $this->streamFactory = $streamFactory;
    }

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
        $request->method('withHeader')->willReturnSelf();
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uri);

        return $request;
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

    /**
     * @return RequestInterface
     */
    public function createMockRequestFromFactory(): RequestInterface
    {
        return $this->createMockRequest(...func_get_args());
    }

    /**
     * @return StreamInterface
     */
    public function createMockStreamFromFactoryFromString(): StreamInterface
    {
        [$body] = func_get_args();

        return $this->createMockStream($body);
    }

    /**
     * @var string $filename
     * @var string $mode
     *
     * @return StreamInterface
     */
    public function createMockStreamFromFactoryFromFile(): StreamInterface
    {
        [$filename, $mode] = func_get_args();

        $resource = fopen($filename, $mode);

        return $this->createMockStreamFromFactoryFromResource($resource);
    }

    /**
     * @return StreamInterface
     */
    public function createMockStreamFromFactoryFromResource(): StreamInterface
    {
        [$resource] = func_get_args();

        return $this->createMockStreamFromFactoryFromString(stream_get_contents($resource));
    }

    /**
     * @param int $count
     * @return array
     */
    public function syntaxError(int $count = 1): array
    {
        $errors = [];

        for ($i = 0; $i < $count; $i++) {
            $errors[] = [
                'message' => 'some syntax error ' . random_int(1000,10000),
                'location' => [
                    [
                        'line' => random_int(1,100),
                        'column' => random_int(1,60),
                    ]
                ],
            ];
        }

        return $errors;
    }
}

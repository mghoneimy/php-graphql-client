<?php

namespace GraphQL\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class TestHelperTest
 *
 * Make sure that the stream and request factory mocks work as real PSR-18
 * implementations returning the correct mock objects
 *
 * @package GraphQL\Tests
 */
class TestHelperTest extends TestCase
{
    /**
     * @covers \GraphQL\Tests\Helper\TestHelper::getStreamFactory
     */
    public function testGetStreamFactory(): void
    {
        $testStringFormat = 'This is a test body from a %s';

        $stringBody = sprintf($testStringFormat, 'string');
        $filenameBody = sprintf($testStringFormat, 'filename');
        $resourceBody = sprintf($testStringFormat, 'resource');

        $phpTemp = 'php://temp';

        $resource = fopen($phpTemp, 'w+');
        $filename = tempnam(sys_get_temp_dir(), 'PHPUNIT_TEST_');

        try {
            fwrite($resource, $resourceBody);
            rewind($resource);
            file_put_contents($filename, $filenameBody);

            $factory = $this->helper->getStreamFactory();

            $stream = $factory->createStream($stringBody);
            $this->assertInstanceOf(StreamInterface::class, $stream);
            $this->assertSame($stringBody, $stream->getContents());

            $resourceStream = $factory->createStreamFromResource($resource);
            $this->assertInstanceOf(StreamInterface::class, $stream);
            $this->assertSame($resourceBody, $resourceStream->getContents());

            $sameFactory = $this->helper->getStreamFactory();
            $this->assertSame($factory, $sameFactory);
        } finally {
            unlink($filename);
            fclose($resource);
        }
    }

    /**
     * @covers \GraphQL\Tests\Helper\TestHelper::getRequestFactory
     */
    public function testGetRequestFactory(): void
    {
        $factory = $this->helper->getRequestFactory();

        $method = 'GET';
        $uri = 'https://fake.uri/';
        $request = $factory->createRequest($method, $uri);

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame($request->getMethod(), $method);
        $this->assertSame($request->getUri(), $uri);

        $sameFactory = $this->helper->getRequestFactory();

        $this->assertSame($factory, $sameFactory);
    }}

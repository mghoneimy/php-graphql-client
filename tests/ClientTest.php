<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 *
 * @package GraphQL\Tests
 */
class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handler = HandlerStack::create($this->mockHandler);
        $this->client      = new MockClient('', $handler);
    }

    /**
     * @covers \GraphQL\Client::__construct
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testConstructClient()
    {
        $mockHandler = new MockHandler();
        $handler     = HandlerStack::create($mockHandler);
        $container   = [];
        $history     = Middleware::history($container);
        $handler->push($history);

        $mockHandler->append(new Response(200));
        $mockHandler->append(new Response(200));

        $client = new MockClient('', $handler);
        $client->runRawQuery('query_string');

        $client = new MockClient('', $handler, ['Authorization' => 'Basic xyz']);
        $client->runRawQuery('query_string');

        /** @var Request $firstRequest */
        $firstRequest = $container[0]['request'];
        $this->assertEquals('{"query":"query_string"}', $firstRequest->getBody()->getContents());

        /** @var Request $secondRequest */
        $secondRequest = $container[1]['request'];
        $this->assertNotNull($secondRequest->getHeader('Authorization'));
        $this->assertEquals(
            ['Basic xyz'],
            $secondRequest->getHeader('Authorization')
        );
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testValidQueryResponse()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                'someField' => [
                    [
                        'data' => 'value',
                    ], [
                        'data' => 'value',
                    ]
                ]
            ]
        ])));

        $objectResults = $this->client->runRawQuery('');
        $this->assertIsObject($objectResults->getResults());
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testValidQueryResponseToArray()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                'someField' => [
                    [
                        'data' => 'value',
                    ], [
                        'data' => 'value',
                    ]
                ]
            ]
        ])));

        $arrayResults = $this->client->runRawQuery('', true);
        $this->assertIsArray($arrayResults->getResults());
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInvalidQueryResponseWith200()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'errors' => [
                [
                    'message' => 'some syntax error',
                    'location' => [
                        [
                            'line' => 1,
                            'column' => 3,
                        ]
                    ],
                ]
            ]
        ])));

        $this->expectException(QueryError::class);
        $this->client->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInvalidQueryResponseWith400()
    {
        $this->mockHandler->append(new ClientException('', new Request('post', ''),
                new Response(400, [], json_encode([
                'errors' => [
                    [
                        'message' => 'some syntax error',
                        'location' => [
                            [
                                'line' => 1,
                                'column' => 3,
                            ]
                        ],
                    ]
                ]
        ]))));

        $this->expectException(QueryError::class);
        $this->client->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testUnauthorizedResponse()
    {
        $this->mockHandler->append(new ClientException('', new Request('post', ''),
                new Response(401, [], json_encode('Unauthorized'))
        ));

        $this->expectException(ClientException::class);
        $this->client->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testNotFoundResponse()
    {
        $this->mockHandler->append(new ClientException('', new Request('post', ''), new Response(404, [])));

        $this->expectException(ClientException::class);
        $this->client->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInternalServerErrorResponse()
    {
        $this->mockHandler->append(new ServerException('', new Request('post', ''), new Response(500, [])));

        $this->expectException(ServerException::class);
        $this->client->runRawQuery('');
    }
}
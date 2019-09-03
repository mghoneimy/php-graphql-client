<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TypeError;

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
        $mockHandler->append(new Response(200));

        $client = new MockClient('', $handler);
        $client->runRawQuery('query_string');

        $client = new MockClient('', $handler, [ 'headers' => [ 'Authorization' => 'Basic xyz'] ]);
        $client->runRawQuery('query_string');

        $client = new MockClient('', $handler);
        $client->runRawQuery('query_string',  false, ['name' => 'val']);

        /** @var Request $firstRequest */
        $firstRequest = $container[0]['request'];
        $this->assertEquals('{"query":"query_string","variables":{}}', $firstRequest->getBody()->getContents());

        /** @var Request $thirdRequest */
        $thirdRequest = $container[1]['request'];
        $this->assertNotNull($thirdRequest->getHeader('Authorization'));
        $this->assertEquals(
            ['Basic xyz'],
            $thirdRequest->getHeader('Authorization')
        );

        /** @var Request $secondRequest */
        $secondRequest = $container[2]['request'];
        $this->assertEquals('{"query":"query_string","variables":{"name":"val"}}', $secondRequest->getBody()->getContents());
    }

    /**
     * @covers \GraphQL\Client::runQuery
     */
    public function testRunQueryBuilder()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                'someData'
            ]
        ])));

        $response = $this->client->runQuery((new QueryBuilder('obj'))->selectField('field'));
        $this->assertNotNull($response->getData());
    }

    /**
     * @covers \GraphQL\Client::runQuery
     */
    public function testRunInvalidQueryClass()
    {
        $this->expectException(TypeError::class);
        $this->client->runQuery(new RawObject('obj'));
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

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testConnectTimeoutResponse()
    {
        $this->mockHandler->append(new ConnectException('Time Out', new Request('post', '')));

        $this->expectException(ConnectException::class);
        $this->client->runRawQuery('');
    }

}
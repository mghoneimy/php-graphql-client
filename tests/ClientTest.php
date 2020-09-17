<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GraphQL\Exception\Client\ClientException;
use GraphQL\Exception\Client\ConnectException;
use GraphQL\Exception\Client\ServerException;
use GraphQL\Exception\QueryError;;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;
use Psr\Http\Message\RequestInterface;
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
    protected $graphQLClient;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->graphQLClient = new Client('', [], [], $this->client);
    }

    /**
     * @covers \GraphQL\Client::__construct
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testConstructClient()
    {
        $response = $this->helper->createMockResponse();

        for ($i = 0; $i < 5; $i++) {
            $this->client->addResponse($response);
        }

        $client = new Client('', [], [], $this->client);
        $client->runRawQuery('query_string');

        $client = new Client('', ['Authorization' => 'Basic xyz'],  [], $this->client);
        $client->runRawQuery('query_string');

        $client = new Client('', [], [], $this->client);
        $client->runRawQuery('query_string',  false, ['name' => 'val']);

        $client = new Client('', ['Authorization' => 'Basic xyz'], ['headers' => [ 'Authorization' => 'Basic zyx', 'User-Agent' => 'test' ]], $this->client);
        $client->runRawQuery('query_string');

        $client = new Client('', ['Authorization' => 'Basic xyz'], ['headers' => [ 'Authorization' => 'Basic zyx', 'User-Agent' => 'test' ]], $this->client, 'GET');
        $client->runRawQuery('query_string');

        $requests = $this->client->getRequests();

        /** @var RequestInterface $firstRequest */
        $firstRequest = $requests[0];
        $this->assertEquals('{"query":"query_string","variables":{}}', $firstRequest->getBody()->getContents());
        $this->assertSame('POST', $firstRequest->getMethod());

        /** @var RequestInterface $thirdRequest */
        $thirdRequest = $requests[1];
        $this->assertNotEmpty($thirdRequest->getHeader('Authorization'));
        $this->assertEquals(
            ['Basic xyz'],
            $thirdRequest->getHeader('Authorization')
        );

        /** @var RequestInterface $secondRequest */
        $secondRequest = $requests[2];
        $this->assertEquals('{"query":"query_string","variables":{"name":"val"}}', $secondRequest->getBody()->getContents());

        /** @var RequestInterface $fourthRequest */
        $fourthRequest = $requests[3];
        $this->assertNotEmpty($fourthRequest->getHeader('Authorization'));
        $this->assertNotEmpty($fourthRequest->getHeader('User-Agent'));
        $this->assertEquals(['Basic zyx'], $fourthRequest->getHeader('Authorization'));
        $this->assertEquals(['test'], $fourthRequest->getHeader('User-Agent'));

        /** @var RequestInterface $fifthRequest */
        $fifthRequest = $requests[4];
        $this->assertSame('GET', $fifthRequest->getMethod());
    }

    /**
     * @covers \GraphQL\Client::runQuery
     */
    public function testRunQueryBuilder()
    {
        $this->client->addResponse($this->helper->createMockResponse(json_encode([
            'data' => [
                'someData'
            ]
        ])));

        $response = $this->graphQLClient->runQuery((new QueryBuilder('obj'))->selectField('field'));
        $this->assertNotNull($response->getData());
    }

    /**
     * @covers \GraphQL\Client::runQuery
     */
    public function testRunInvalidQueryClass()
    {
        $this->expectException(TypeError::class);
        $this->graphQLClient->runQuery(new RawObject('obj'));
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testValidQueryResponse()
    {
        $this->client->addResponse($this->helper->createMockResponse(json_encode([
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

        $objectResults = $this->graphQLClient->runRawQuery('');
        $this->assertIsObject($objectResults->getResults());
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testValidQueryResponseToArray()
    {
        $this->client->addResponse($this->helper->createMockResponse(json_encode([
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

        $arrayResults = $this->graphQLClient->runRawQuery('', true);
        $this->assertIsArray($arrayResults->getResults());
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInvalidQueryResponseWith200()
    {
        $this->client->addResponse($this->helper->createMockResponse(json_encode([
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
        $this->graphQLClient->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInvalidQueryResponseWith400()
    {
        $mockRequest = $this->helper->mockRequest('', 'POST', json_encode([
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
        ]), '400');
        $this->client->addResponse($mockRequest->getResponse());
        $this->expectException(QueryError::class);
        $this->graphQLClient->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testUnauthorizedResponse()
    {
        $mockRequest = $this->helper->mockRequest('', 'POST', json_encode('Unauthorized'), 401);
        $this->client->addResponse($mockRequest->getResponse());
        $this->expectException(ClientException::class);
        $this->graphQLClient->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testNotFoundResponse()
    {
        $mockRequest = $this->helper->mockRequest('', 'POST', json_encode('Not Found'), 404);
        $this->client->addResponse($mockRequest->getResponse());
        $this->expectException(ClientException::class);
        $this->graphQLClient->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testInternalServerErrorResponse()
    {
        $mockRequest = $this->helper->mockRequest('', 'POST', json_encode('Internal Error'), 500);
        $this->client->addResponse($mockRequest->getResponse());
        $this->expectException(ServerException::class);
        $this->graphQLClient->runRawQuery('');
    }

    /**
     * @covers \GraphQL\Client::runRawQuery
     */
    public function testConnectTimeoutResponse()
    {
        $this->client->addException($this->helper->createMockNetworkException());
        $this->expectException(ConnectException::class);
        $this->graphQLClient->runRawQuery('');
    }
}

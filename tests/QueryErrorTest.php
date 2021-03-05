<?php

namespace GraphQL\Tests;

use GraphQL\Exception\QueryError;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryErrorTest
 *
 * @package GraphQL\Tests
 */
class QueryErrorTest extends TestCase
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
        $this->client      = new Client(['handler' => $this->mockHandler]);
    }

    /**
     * @covers \GraphQL\Exception\QueryError::__construct
     * @covers \GraphQL\Exception\QueryError::getErrorDetails
     */
    public function testConstructQueryError()
    {
        $exceptionMessage = 'some syntax error';
        $errorData = [
            'errors' => [
                [
                    'message' => $exceptionMessage,
                    'location' => [
                        [
                            'line' => 1,
                            'column' => 3,
                        ]
                    ],
                ]
            ]
        ];

        $request = new Request('POST', '');
        $response = $this->client->post('', []);
        $queryError = new QueryError($errorData, $request, $response);
        $this->assertEquals($exceptionMessage, $queryError->getMessage());
        $this->assertEquals(
            [
                'message' => 'some syntax error',
                'location' => [
                    [
                        'line' => 1,
                        'column' => 3,
                    ]
                ]
            ],
            $queryError->getErrorDetails()
        );
    }
}
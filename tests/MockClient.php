<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GuzzleHttp\Handler\MockHandler;

/**
 * Class MockClient
 *
 * @package GraphQL\Tests
 */
class MockClient extends Client
{
    public function __construct(string $endpointUrl, MockHandler $mockHandler, array $authorizationHeaders = [])
    {
        parent::__construct($endpointUrl, $authorizationHeaders);
        $this->httpClient = new \GuzzleHttp\Client(['handler' => $mockHandler]);
    }
}
<?php

namespace GraphQL\Tests;

use GraphQL\Client;

/**
 * Class MockClient
 *
 * @package GraphQL\Tests
 */
class MockClient extends Client
{
    /**
     * MockClient constructor.
     *
     * @param string $endpointUrl
     * @param object $handler
     * @param array $authorizationHeaders
     * @param array $httpOptions
     */
    public function __construct(string $endpointUrl, $handler, array $authorizationHeaders = [], array $httpOptions = [])
    {
        parent::__construct($endpointUrl, $authorizationHeaders, $httpOptions);
        $this->httpClient = new \GuzzleHttp\Client(['handler' => $handler]);
    }
}
<?php

namespace GraphQL;

use GraphQL\Exception\QueryError;
use GuzzleHttp\Exception\ClientException;

/**
 * Class Client
 *
 * @package GraphQL
 */
class Client
{
    /**
     * @var string
     */
    protected $endpointUrl;

    /**
     * @var array
     */
    protected $authorizationHeaders;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Client constructor.
     *
     * @param string $endpointUrl
     * @param array  $authorizationHeaders
     */
    public function __construct(string $endpointUrl, array $authorizationHeaders = [])
    {
        $this->endpointUrl          = $endpointUrl;
        $this->authorizationHeaders = $authorizationHeaders;
        $this->httpClient           = new \GuzzleHttp\Client();
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Query $query
     * @param bool  $resultsAsArray
     *
     * @return Results|null
     * @throws QueryError
     */
    public function runQuery(Query $query, bool $resultsAsArray = false): ?Results
    {
        return $this->runRawQuery((string) $query, $resultsAsArray);
    }

    /**
     * @param string $queryString
     * @param bool   $resultsAsArray
     *
     * @return Results|null
     * @throws QueryError
     *
     * TODO: Rename this to runStringQuery on v1.0
     */
    public function runRawQuery(string $queryString, $resultsAsArray = false): ?Results
    {
        // Set request headers for authorization and content type
        if (!empty($this->authorizationHeaders)) {
            $options['headers'] = $this->authorizationHeaders;
        }
        $options['headers']['Content-Type'] = 'application/json';

        // Set query in the request body
        $options['body'] = json_encode(['query' => (string) $queryString]);

        // Send api request and get response
        try {
            $response = $this->httpClient->post($this->endpointUrl, $options);
        }
        catch (ClientException $exception) {
            $response = $exception->getResponse();

            // If exception thrown by client is "400 Bad Request ", then it can be treated as a successful API request
            // with a syntax error in the query, otherwise the exceptions will be propagated
            if ($response->getStatusCode() !== 400) {
                throw $exception;
            }
        }

        // Parse response to extract results
        $results = new Results($response, $resultsAsArray);

        return $results;
    }
}
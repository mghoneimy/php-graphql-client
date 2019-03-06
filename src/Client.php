<?php

namespace GraphQL;

use GraphQL\Exception\QueryError;
use GraphQL\SchemaObject\QueryObject;

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
     * @param QueryObject $queryObject
     * @param bool        $resultsAsArray
     *
     * @return Results|null
     * @throws Exception\EmptySelectionSetException
     * @throws QueryError
     */
    public function runQueryObject(QueryObject $queryObject, bool $resultsAsArray = false): ?Results
    {
        return $this->runRawQuery($queryObject->getQueryString(), $resultsAsArray);
    }

    /**
     * @param string $queryString
     * @param bool   $resultsAsArray
     *
     * @return Results|null
     * @throws QueryError
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
        $response = $this->httpClient->post($this->endpointUrl, $options);

        // Parse response to extract results
        $results = null;
        if ($response->getStatusCode() === 200) {
            $results = new Results($response, $resultsAsArray);
        }

        return $results;
    }
}
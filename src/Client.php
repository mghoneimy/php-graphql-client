<?php

namespace GraphQL;

use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilderInterface;
use GuzzleHttp\Exception\ClientException;
use TypeError;

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
     * @var array
     */
    protected $httpOptions;


    /**
     * Client constructor.
     *
     * @param string $endpointUrl
     * @param array $authorizationHeaders
     * @param array $httpOptions
     */
    public function __construct(string $endpointUrl, array $authorizationHeaders = [], array $httpOptions = [])
    {
        $this->endpointUrl          = $endpointUrl;
        $this->authorizationHeaders = $authorizationHeaders;
        $this->httpClient           = new \GuzzleHttp\Client();
        $this->httpOptions          = $httpOptions;
    }

    /**
     * @param Query|QueryBuilderInterface $query
     * @param bool                        $resultsAsArray
     * @param array                       $variables
     *
     * @return Results
     * @throws QueryError
     */
    public function runQuery($query, bool $resultsAsArray = false, array $variables = []): Results
    {
        if ($query instanceof QueryBuilderInterface) {
            $query = $query->getQuery();
        }

        if (!$query instanceof Query) {
            throw new TypeError('Client::runQuery accepts the first argument of type Query or QueryBuilderInterface');
        }

        return $this->runRawQuery((string) $query, $resultsAsArray, $variables);
    }

    /**
     * @param string $queryString
     * @param bool   $resultsAsArray
     * @param array  $variables
     *
     * @return Results
     * @throws QueryError
     */
    public function runRawQuery(string $queryString, $resultsAsArray = false, array $variables = []): Results
    {
        // Set request headers for authorization and content type
        if (!empty($this->authorizationHeaders)) {
            $options['headers'] = $this->authorizationHeaders;
        }

        // Set request options for \GuzzleHttp\Client
        if (!empty($this->httpOptions)) {
            $options = $this->httpOptions;
        }

        $options['headers']['Content-Type'] = 'application/json';

        // Convert empty variables array to empty json object
        if (empty($variables)) $variables = (object) null;
        // Set query in the request body
        $bodyArray       = ['query' => (string) $queryString, 'variables' => $variables];
        $options['body'] = json_encode($bodyArray);

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
<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL;

use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilderInterface;
use GraphQL\Util\GuzzleAdapter;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use TypeError;

/**
 * Class Client.
 */
class Client
{
    /**
     * @var string
     */
    protected $endpointUrl;

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $httpHeaders;

    /**
     * @var string
     */
    protected $requestMethod;

    /**
     * Client constructor.
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(
        string $endpointUrl,
        array $authorizationHeaders = [],
        array $httpOptions = [],
        ClientInterface $httpClient = null,
        string $requestMethod = 'POST'
    ) {
        $headers = array_merge(
            $authorizationHeaders,
            $httpOptions['headers'] ?? [],
            ['Content-Type' => 'application/json']
        );

        /*
         * All headers will be set on the request objects explicitly,
         * Guzzle doesn't have to care about them at this point, so to avoid any conflicts
         * we are removing the headers from the options
         */
        unset($httpOptions['headers']);

        $this->endpointUrl = $endpointUrl;
        $this->httpClient = $httpClient ?? new GuzzleAdapter(new \GuzzleHttp\Client($httpOptions));
        $this->httpHeaders = $headers;
        $this->requestMethod = $requestMethod;
    }

    /**
     * @param Query|QueryBuilderInterface $query
     *
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
     * @param bool $resultsAsArray
     * @param
     *
     * @throws QueryError
     */
    public function runRawQuery(string $queryString, $resultsAsArray = false, array $variables = []): Results
    {
        $request = new Request($this->requestMethod, $this->endpointUrl);

        foreach ($this->httpHeaders as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        // Convert empty variables array to empty json object
        if (empty($variables)) {
            $variables = (object) null;
        }
        // Set query in the request body
        $bodyArray = ['query' => (string) $queryString, 'variables' => $variables];
        $request = $request->withBody(Psr7\stream_for(json_encode($bodyArray)));

        // Send api request and get response
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();

            // If exception thrown by client is "400 Bad Request ", then it can be treated as a successful API request
            // with a syntax error in the query, otherwise the exceptions will be propagated
            if (400 !== $response->getStatusCode()) {
                throw $exception;
            }
        }

        // Parse response to extract results
        return new Results($response, $resultsAsArray);
    }
}

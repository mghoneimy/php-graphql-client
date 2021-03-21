<?php

namespace GraphQL;

use GraphQL\QueryBuilder\QueryBuilderInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
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
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var RequestDecorator
     */
    protected $requestDecorator;

    /**
     * @var array
     */
    protected $httpHeaders;

    /**
     * Client constructor.
     *
     * @param string                       $endpointUrl
     * @param array                        $httpHeaders
     * @param ClientInterface|null         $httpClient
     * @param StreamFactoryInterface|null  $streamFactory
     * @param RequestFactoryInterface|null $requestFactory
     * @param RequestDecorator|null        $requestDecorator
     */
    public function __construct(
        string $endpointUrl,
        array $httpHeaders = [],
        ClientInterface $httpClient = null,
        StreamFactoryInterface $streamFactory = null,
        RequestFactoryInterface $requestFactory = null,
        RequestDecorator $requestDecorator = null
    ) {
        $this->httpHeaders = array_merge(
            $httpHeaders,
            ['Content-Type' => 'application/json']
        );

        $this->endpointUrl          = $endpointUrl;
        $this->httpClient           = $httpClient ?? Psr18ClientDiscovery::find();
        $this->streamFactory        = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
        $this->requestFactory       = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->requestDecorator     = $requestDecorator ?? new RequestDecorator();
    }

    /**
     * @param Query|QueryBuilderInterface|RawObject $query
     * @param bool                                  $resultsAsArray
     * @param array                                 $variables
     * @param string                                $method
     *
     * @return Results
     */
    public function runQuery(
        $query,
        bool $resultsAsArray = false,
        array $variables = [],
        string $method = 'POST'
    ): Results
    {
        if ($query instanceof QueryBuilderInterface) {
            $query = $query->getQuery();
        }

        if (!$query instanceof Query) {
            throw new TypeError('Client::runQuery accepts the first argument of type Query or QueryBuilderInterface');
        }

        return $this->runRawQuery((string) $query, $resultsAsArray, $variables, $method);
    }

    /**
     * @param string $queryString
     * @param bool   $resultsAsArray
     * @param array  $variables
     * @param string $requestMethod
     *
     * @return Results
     * @throws ClientExceptionInterface
     */
    public function runRawQuery(
        string $queryString, $resultsAsArray = false, array $variables = [], string $requestMethod = 'POST'): Results
    {
        $request = $this->requestFactory->createRequest($requestMethod, $this->endpointUrl);

        // Convert empty variables array to empty json object
        if (empty($variables)) $variables = (object) null;

        // Set query in the request body
        $bodyArray = ['query' => (string) $queryString, 'variables' => $variables];

        $stream = $this->streamFactory->createStream(json_encode($bodyArray));

        $request = $this->requestDecorator->decorate($request, $stream, $this->httpHeaders);

        // Send api request and get response
        $response = $this->httpClient->sendRequest($request);

        // Parse response to extract results
        return new Results($response, $resultsAsArray);
    }
}

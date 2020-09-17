<?php

namespace GraphQL;

use GraphQL\Exception\InvalidArgumentException;
use GraphQL\Exception\QueryError;
use GraphQL\Exception\Client\ConnectException;
use GraphQL\Exception\Client\RequestException;
use GraphQL\QueryBuilder\QueryBuilderInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use InvalidArgumentException as BaseInvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @var array
     */
    protected $httpHeaders;

    /**
     * @var array
     */
    protected $httpOptions;

    /**
     * @var string
     */
    protected $requestMethod;

    /**
     * Client constructor.
     * @param string                       $endpointUrl
     * @param array                        $authorizationHeaders
     * @param array                        $httpOptions
     * @param ClientInterface|null         $httpClient
     * @param StreamFactoryInterface|null  $streamFactory
     * @param RequestFactoryInterface|null $requestFactory
     * @param string                       $requestMethod
     */
    public function __construct(
        string $endpointUrl,
        array $authorizationHeaders = [],
        array $httpOptions = [],
        ClientInterface $httpClient = null,
        string $requestMethod = 'POST',
        StreamFactoryInterface $streamFactory = null,
        RequestFactoryInterface $requestFactory = null
    ) {
        $headers = array_merge(
            $authorizationHeaders,
            $httpOptions['headers'] ?? [],
            ['Content-Type' => 'application/json']
        );

        $this->endpointUrl          = $endpointUrl;
        $this->httpOptions          = $httpOptions;
        $this->httpClient           = $httpClient ?? Psr18ClientDiscovery::find();
        $this->streamFactory        = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
        $this->requestFactory       = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->httpHeaders          = $headers;
        $this->requestMethod        = $requestMethod;
    }

    /**
     * @param Query|QueryBuilderInterface|RawObject $query
     * @param bool                                  $resultsAsArray
     * @param array                                 $variables
     *
     * @return Results
     * @throws QueryError
     */
    public function runQuery(
        $query,
        bool $resultsAsArray = false,
        array $variables = []
    ): Results
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
     * @param
     *
     * @return Results
     * @throws QueryError
     */
    public function runRawQuery(string $queryString, $resultsAsArray = false, array $variables = []): Results
    {
        $request = $this->requestFactory->createRequest($this->requestMethod, $this->endpointUrl);

        // Convert empty variables array to empty json object
        if (empty($variables)) $variables = (object) null;

        // Set query in the request body
        $bodyArray = ['query' => (string) $queryString, 'variables' => $variables];

        $stream = $this->streamFactory->createStream(json_encode($bodyArray));

        $request = (new RequestDecorator())->decorate($request, $stream, $this->httpOptions, $this->httpHeaders);

        try {
            $request = $request->withBody($stream);

            foreach($this->httpHeaders as $header => $value) {
                $request = $request->withHeader($header, $value);
            }

            // Send api request and get response
            $response = $this->httpClient->sendRequest($request);
        }
        catch (BaseInvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
        catch (RequestExceptionInterface $e) {
            throw RequestException::create($e->getRequest(), null, $e);
        }
        catch (NetworkExceptionInterface $e) {
            throw ConnectException::create($e->getRequest(), null, $e);
        }
        catch (ClientExceptionInterface $e) {
            throw RequestException::create($request, null, $e);
        }

        if ($this->shouldThrowException($response)) {
            throw RequestException::create($request, $response);
        }

        // Parse response to extract results
        return new Results($response, $resultsAsArray);
    }

    /**
     * @param ResponseInterface|null $response
     * @return bool
     */
    private function shouldThrowException(?ResponseInterface $response): bool
    {
        if (!$response) {
            return true;
        }

        $statusCode = $response->getStatusCode();

        // If exception thrown by client is "400 Bad Request ", then it can be treated as a successful API request
        // with a syntax error in the query, otherwise the exceptions will be propagated
        return $statusCode !== 400
            && (int) floor($statusCode / 100) !== 2;
    }
}

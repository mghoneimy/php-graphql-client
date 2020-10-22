<?php

namespace GraphQL;

use GraphQL\Exception\QueryError;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Result
 *
 * @package GraphQl
 */
class Results
{
    /**
     * @var string
     */
    private const CLIENT_ERROR = 'ClientError';

    /**
     * @var string
     */
    private const SERVER_ERROR = 'ServerError';

    /**
     * @var string
     */
    private const QUERY_ERROR = 'QueryError';

    /**
     * @var string
     */
    private const INVALID_RESPONSE_ERROR = 'InvalidResponseError';

    /**
     * @var string
     */
    private const GRAPHQL_ERRORS_KEY = 'errors';

    /**
     * @var string
     */
    protected $responseBody;

    /**
     * @var ResponseInterface
     */
    protected $responseObject;

    /**
     * @var array|object
     */
    protected $results;

    /**
     * @var bool
     */
    protected $asArray;

    /**
     * @var int
     */
    protected $responseStatusCode;

    /**
     * @var string|null
     */
    protected $errorType;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Result constructor.
     *
     * Receives json response from GraphQL api response and parses it as associative array or nested object accordingly
     *
     * @param ResponseInterface $response
     * @param bool              $asArray
     *
     * @throws QueryError
     */
    public function __construct(ResponseInterface $response, $asArray = false)
    {
        $this->responseObject     = $response;
        $this->responseBody       = $this->responseObject->getBody()->getContents();
        $this->asArray            = $asArray;
        $this->results            = json_decode($this->responseBody, $this->asArray);
        $this->responseStatusCode = $this->responseObject->getStatusCode();
        $this->errorType          = $this->extractErrorType();
        $this->errors             = $this->extractErrors();
    }

    /**
     * @param bool $asArray
     */
    public function reformatResults(bool $asArray): void
    {
        $this->results = json_decode($this->responseBody, $asArray);
    }

    /**
     * Returns only parsed data objects in the requested format
     *
     * @return array|object
     */
    public function getData()
    {
        if (is_array($this->results)) {
            return $this->results['data'];
        }

        return $this->results->data;
    }

    /**
     * Returns entire parsed results in the requested format
     *
     * @return array|object
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseObject(): ResponseInterface
    {
        return $this->responseObject;
    }

    /**
     * @return int
     */
    public function getResponseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @return string|null
     */
    public function getErrorType(): ?string
    {
        return $this->errorType;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getError(): array
    {
        $errors = $this->getErrors();

        return array_shift($errors);
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getKeyFromResults(string $key)
    {
        $value = $this->results;

        foreach (explode(".", $key) as $keyPart) {
            $value = $this->asArray
                ? ($value[$keyPart] ?? null)
                : ($value->$keyPart ?? null);

            if ($value === null) {
                break;
            }
        }

        return $value;
    }

    /**
     * @return string|null
     */
    private function extractErrorType(): ?string
    {
        $level = (int) floor($this->responseStatusCode / 100);

        if ($level === 4) {
            return self::CLIENT_ERROR;
        }

        if ($level === 5) {
            return self::SERVER_ERROR;
        }

        if ($this->getKeyFromResults(self::GRAPHQL_ERRORS_KEY)) {
            return self::QUERY_ERROR;
        }

        if (empty($this->results)) {
            return self::INVALID_RESPONSE_ERROR;
        }

        return null;
    }

    /**
     * @return array
     */
    private function extractErrors(): array
    {
        switch ($this->errorType) {
            case self::QUERY_ERROR:
                return (array) $this->getKeyFromResults(self::GRAPHQL_ERRORS_KEY);
            case self::SERVER_ERROR:
            case self::CLIENT_ERROR:
            case self::INVALID_RESPONSE_ERROR:
                if (!is_null($this->results)) {
                    return [$this->results];
                }

                $error = ['response' => $this->responseBody];
                $error = $this->asArray ? $error : (object) $error;

                return [$error];
        }

        return [];
    }
}

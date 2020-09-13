<?php

namespace GraphQL\Exception\Client;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class RequestException
 *
 * @package GraphQL\Exception\Request
 */
class RequestException extends TransferException implements RequestExceptionInterface
{
    /**
     * @var string
     */
    protected const DEFAULT_ERROR_MESSAGE = 'Error completing request';

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * RequestException constructor.
     * @param string                 $message
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     * @param Throwable|null         $previous
     */
    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        $code = $response ? $response->getStatusCode() : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Factory method to create a new exception with a normalized error message
     *
     * @param RequestInterface       $request  Request
     * @param ResponseInterface|null $response Response received
     * @param Throwable|null         $previous Previous exception
     *
     * @return self
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response = null,
        Throwable $previous = null
    ) {
        if (!$response) {
            return new static(
                $previous ? $previous->getMessage() : static::DEFAULT_ERROR_MESSAGE,
                $request,
                null,
                $previous
            );
        }

        $level = (int) floor($response->getStatusCode() / 100);

        if ($level === 4) {
            $label = 'Client error';
            $className = ClientException::class;
        } else if ($level === 5) {
            $label = 'Server error';
            $className = ServerException::class;
        } else {
            $label = 'Unsuccessful request';
            $className = __CLASS__;
        }

        // Client Error: `GET /` resulted in a `404 Not Found` response:
        // <html> ... (truncated)
        $message = sprintf(
            '%s: `%s %s` resulted in a `%s %s` response',
            $label,
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return new $className($message, $request, $response, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Check if a response was received
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
}

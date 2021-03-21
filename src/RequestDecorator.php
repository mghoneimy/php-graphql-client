<?php

namespace GraphQL;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class RequestDecorator
 * @package GraphQL\Client
 */
class RequestDecorator
{
    /**
     * @param RequestInterface $request
     * @param StreamInterface            $httpBody
     * @param array            $httpHeaders
     * @return RequestInterface
     */
    public function decorate(
        RequestInterface $request,
        StreamInterface $httpBody,
        array $httpHeaders = []
    ): RequestInterface
    {
        $request = $request->withBody($httpBody);

        foreach($httpHeaders as $header => $value) {
            $request = $request->withHeader($header, $value);
        }

        return $request;
    }
}

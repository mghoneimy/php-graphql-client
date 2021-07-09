<?php

namespace GraphQL\Auth;

use GuzzleHttp\Psr7\Request;

class HeaderAuth implements AuthInterface
{
    /**
     * @param Request $request
     * @param array $headers
     * @return Request
     */
    public function run(Request $request, array $headers = []): Request
    {
        foreach ($headers as $header => $value) {
            $request = $request->withHeader($header, $value);
        }
        return $request;
    }
}

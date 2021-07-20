<?php

namespace GraphQL\Auth;

use GuzzleHttp\Psr7\Request;

interface AuthInterface
{
    /**
     * @param Request $request
     * @param array $headers
     * @return Request
     */
    public function run(Request $request, array $headers = []): Request;
}

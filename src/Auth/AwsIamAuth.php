<?php

namespace GraphQL\Auth;

use Aws\Credentials\CredentialProvider;
use GuzzleHttp\Psr7\Request;

class AwsIamAuth implements AuthInterface
{
    protected const SERVICE_NAME = 'appsync';

    /**
     * @param Request $request
     * @param array $headers
     * @return Request
     */
    public function run(Request $request, array $headers = []): Request
    {
        $region = getenv('AWS_REGION');
        $signature = new \Aws\Signature\SignatureV4('appsync', $region);
        return $signature->signRequest(
            $request, $this->getCredentials()->wait(),
            self::SERVICE_NAME
        );
    }

    protected function getCredentials()
    {
        $provider = CredentialProvider::env();
        return $provider();
    }
}

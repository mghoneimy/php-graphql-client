<?php

namespace GraphQL\Exception\Client;

use GraphQL\Exception\Exception;
use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

/**
 * Class TransferException
 *
 * @package GraphQL\Exception\Request
 */
class TransferException extends RuntimeException implements ClientExceptionInterface, Exception
{
}

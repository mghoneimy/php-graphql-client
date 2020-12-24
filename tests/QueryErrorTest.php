<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\Exception\QueryError;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryErrorTest.
 *
 * @coversNothing
 */
class QueryErrorTest extends TestCase
{
    /**
     * @covers \GraphQL\Exception\QueryError::__construct
     * @covers \GraphQL\Exception\QueryError::getErrorDetails
     */
    public function testConstructQueryError()
    {
        $exceptionMessage = 'some syntax error';
        $errorData = [
            'errors' => [
                [
                    'message' => $exceptionMessage,
                    'location' => [
                        [
                            'line' => 1,
                            'column' => 3,
                        ],
                    ],
                ],
            ],
        ];

        $queryError = new QueryError($errorData);
        $this->assertSame($exceptionMessage, $queryError->getMessage());
        $this->assertSame(
            [
                'message' => 'some syntax error',
                'location' => [
                    [
                        'line' => 1,
                        'column' => 3,
                    ],
                ],
            ],
            $queryError->getErrorDetails()
        );
    }
}

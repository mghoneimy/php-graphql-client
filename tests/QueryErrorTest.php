<?php

namespace GraphQL\Tests;

use GraphQL\Exception\QueryError;
use PHPUnit\Framework\TestCase;

/**
 * Class QueryErrorTest
 *
 * @package GraphQL\Tests
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
                        ]
                    ],
                ]
            ]
        ];

        $queryError = new QueryError($errorData);
        $this->assertEquals($exceptionMessage, $queryError->getMessage());
        $this->assertEquals(
            [
                'message' => 'some syntax error',
                'location' => [
                    [
                        'line' => 1,
                        'column' => 3,
                    ]
                ]
            ],
            $queryError->getErrorDetails()
        );
        $this->assertEquals([], $queryError->getData());
    }

    /**
     * @covers \GraphQL\Exception\QueryError::__construct
     * @covers \GraphQL\Exception\QueryError::getErrorDetails
     */
    public function testConstructQueryErrorWhenResponseHasData()
    {
        $errorData = [
            'errors' => [
                [
                    'message' => 'first error message',
                    'location' => [
                        [
                            'line' => 1,
                            'column' => 3,
                        ]
                    ],
                ],
                [
                    'message' => 'second error message',
                    'location' => [
                        [
                            'line' => 2,
                            'column' => 4,
                        ]
                    ],
                ],
            ],
            'data' => [
                'someField' => [
                    [
                        'data' => 'value',
                    ],
                    [
                        'data' => 'value',
                    ]
                ]
            ]
        ];

        $queryError = new QueryError($errorData);
        $this->assertEquals('first error message', $queryError->getMessage());
        $this->assertEquals(
            [
                'message' => 'first error message',
                'location' => [
                    [
                        'line' => 1,
                        'column' => 3,
                    ]
                ]
            ],
            $queryError->getErrorDetails()
        );

        $this->assertEquals(
            [
                [
                    'message' => 'first error message',
                    'location' => [
                        [
                            'line' => 1,
                            'column' => 3,
                        ]
                    ]
                ],
                [
                    'message' => 'second error message',
                    'location' => [
                        [
                            'line' => 2,
                            'column' => 4,
                        ]
                    ]
                ]
            ],
            $queryError->getErrors()
        );

        $this->assertEquals(
            [
                'someField' => [
                    [
                        'data' => 'value',
                    ],
                    [
                        'data' => 'value',
                    ]
                ]
            ],
            $queryError->getData()
        );
    }
}

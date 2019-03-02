<?php

use GraphQL\SchemaGenerator\SchemaScanner;

class SchemaScannerTest extends CodeFileTestCase
{
    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/schema_objects';
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaScanner::setWriteDir
     * @covers \GraphQL\SchemaGenerator\SchemaScanner::getWriteDir
     */
    public function testSetWriteDirectory()
    {
        $schemaScanner = new SchemaScanner();
        $this->assertStringEndsWith('/graphql-client/schema_object', $schemaScanner->getWriteDir());
    }

    /**
     * @param array $schemaTypes
     * @param array $expectedFileNames
     *
     * @dataProvider schemaStringProvider
     * 
     * @covers \GraphQL\SchemaGenerator\SchemaScanner::generateSchemaObjects
     */
    public function testSchemaTypesReading(array $schemaTypes, array $expectedFileNames)
    {
        $schemaScanner = new SchemaScanner();
        $schemaScanner->generateSchemaObjects($schemaTypes, static::getGeneratedFilesDir());

        foreach ($expectedFileNames as $expectedFileName) {
            $this->assertFileEquals(
                static::getExpectedFilesDir() . "/$expectedFileName.php",
                static::getGeneratedFilesDir() . "/$expectedFileName.php"
            );
        }
    }

    /**
     * @return array
     */
    public function schemaStringProvider()
    {
        $simpleObjectSchema = [
            [
                'name' => 'SimpleObject',
                'description' => '',
                'type' => [
                    'name' => null,
                    'kind' => 'LIST',
                    'ofType' => [
                        'name' => 'SimpleObject',
                        'kind' => 'OBJECT',
                        'description' => 'some description',
                        'fields' =>[
                            [
                                'name' => '_id',
                                'description' => 'object id',
                                'type' => [
                                    'name' => 'Long',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ], [
                                'name' => 'name',
                                'description' => 'name of object',
                                'type' => [
                                    'name' => 'String',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ], [
                                'name' => 'creation_date',
                                'description' => 'object creation date',
                                'type' => [
                                    'name' => 'String',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ]
                        ],
                    ],
                ],
                'args' => [
                    [
                        'name' => '_id',
                        'description' => '',
                        'type' => [
                            'name' => 'LONG',
                            'description' => 'Long type',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ], [
                        'name' => '_ids',
                        'description' => '',
                        'type' => [
                            'name' => null,
                            'description' => null,
                            'kind' => 'LIST',
                            'inputFields' => null,
                            'ofType' => [
                                'name' => 'String',
                                'description' => 'Build-in String',
                                'kind' => 'SCALAR',
                                'enumValues' => null
                            ],
                        ]
                    ], [
                        'name' => 'name',
                        'description' => '',
                        'type' => [
                            'name' => 'String',
                            'description' => 'Built-in String',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ], [
                        'name' => 'names',
                        'description' => '',
                        'type' => [
                            'name' => null,
                            'description' => null,
                            'kind' => 'LIST',
                            'inputFields' => null,
                            'ofType' => [
                                'name' => 'String',
                                'description' => 'Build-in String',
                                'kind' => 'SCALAR',
                                'enumValues' => null
                            ],
                        ]
                    ], [
                        'name' => 'creation_date',
                        'description' => '',
                        'type' => [
                            'name' => 'String',
                            'description' => 'Built-in String',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ],
                ],
            ],
        ];
        
        $complexObjectSchema = [
            [
                'name' => 'ComplexObject',
                'description' => '',
                'type' => [
                    'name' => null,
                    'kind' => 'LIST',
                    'ofType' => [
                        'name' => 'ComplexObject',
                        'kind' => 'OBJECT',
                        'description' => 'some description',
                        'fields' =>[
                            [
                                'name' => '_id',
                                'description' => 'object id',
                                'type' => [
                                    'name' => 'Long',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ], [
                                'name' => 'simples',
                                'description' => 'related simple objects',
                                'type' => [
                                    'name' => null,
                                    'kind' => 'LIST',
                                    'ofType' => [
                                        'name' => 'SimpleObject',
                                        'kind' => 'OBJECT'
                                    ]
                                ]
                            ], [
                                'name' => 'creation_date',
                                'description' => 'object creation date',
                                'type' => [
                                    'name' => 'String',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ]
                        ],
                    ],
                ],
                'args' => [
                    [
                        'name' => '_id',
                        'description' => '',
                        'type' => [
                            'name' => 'LONG',
                            'description' => 'Long type',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ],
                    [
                        'name' => 'creation_date',
                        'description' => '',
                        'type' => [
                            'name' => 'String',
                            'description' => 'Built-in String',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ],
                ],
            ],
        ];

        $orderingObjectName = '_Ordering';
        $withEnumsObjectSchema = [
            [
                'name' => 'WithEnum',
                'description' => '',
                'type' => [
                    'name' => null,
                    'kind' => 'LIST',
                    'ofType' => [
                        'name' => 'WithEnum',
                        'kind' => 'OBJECT',
                        'description' => 'some description',
                        'fields' =>[
                            [
                                'name' => '_id',
                                'description' => 'object id',
                                'type' => [
                                    'name' => 'Long',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ], [
                                'name' => 'creation_date',
                                'description' => 'object creation date',
                                'type' => [
                                    'name' => 'String',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ]
                        ],
                    ],
                ],
                'args' => [
                    [
                        'name' => '_id',
                        'description' => '',
                        'type' => [
                            'name' => 'LONG',
                            'description' => 'Long type',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ],
                    [
                        'name' => 'creation_date',
                        'description' => '',
                        'type' => [
                            'name' => 'String',
                            'description' => 'Built-in String',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ],
                    [
                        'name' => 'orderBy',
                        'description' => null,
                        'type' => [
                            'name' => null,
                            'description' => null,
                            'kind' => 'LIST',
                            'inputFields' => null,
                            'ofType' => [
                                'name' => $orderingObjectName,
                                'description' => 'ordering of this object',
                                'kind' => 'ENUM',
                                'enumValues' => [
                                    [
                                        'name' => 'creation_date_asc',
                                        'description' => 'Ascending order of creation date',
                                    ],
                                    [
                                        'name' => 'creation_date_desc',
                                        'description' => 'Descending order of creation date',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ],
        ];

        $inputObjectName = '_FilterBy';
        $withInputObjectSchema = [
            [
                'name' => 'WithInput',
                'description' => '',
                'type' => [
                    'name' => null,
                    'kind' => 'LIST',
                    'ofType' => [
                        'name' => 'WithInput',
                        'kind' => 'OBJECT',
                        'description' => 'some description',
                        'fields' =>[
                            [
                                'name' => '_id',
                                'description' => 'object id',
                                'type' => [
                                    'name' => 'Long',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ], [
                                'name' => 'name',
                                'description' => 'object name',
                                'type' => [
                                    'name' => 'String',
                                    'kind' => 'SCALAR',
                                    'ofType' => null
                                ]
                            ]
                        ],
                    ],
                ],
                'args' => [
                    [
                        'name' => '_id',
                        'description' => '',
                        'type' => [
                            'name' => 'LONG',
                            'description' => 'Long type',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ], [
                        'name' => 'name',
                        'description' => '',
                        'type' => [
                            'name' => 'String',
                            'description' => 'Build-in String',
                            'kind' => 'SCALAR',
                            'inputFields' => null,
                            'ofType' => null,
                        ]
                    ], [
                        'name' => 'filterBy',
                        'description' => '',
                        'type' => [
                            'name' => $inputObjectName,
                            'description' => 'Filter input object',
                            'kind' => 'INPUT_OBJECT',
                            'inputFields' => [
                                [
                                    'name' => 'name',
                                    'description' => '',
                                    'type' => [
                                        'name' => 'String',
                                        'description' => '',
                                        'kind' => 'SCALAR',
                                        'ofType' => null,
                                    ]
                                ], [
                                    'name' => 'name_contains',
                                    'description' => '',
                                    'type' => [
                                        'name' => 'String',
                                        'description' => '',
                                        'kind' => 'SCALAR',
                                        'ofType' => null,
                                    ]
                                ], [
                                    'name' => 'name_not',
                                    'description' => '',
                                    'type' => [
                                        'name' => 'String',
                                        'description' => '',
                                        'kind' => 'SCALAR',
                                        'ofType' => null,
                                    ]
                                ], [
                                    'name' => 'name_in',
                                    'description' => '',
                                    'type' => [
                                        'name' => null,
                                        'description' => '',
                                        'kind' => 'LIST',
                                        'ofType' => [
                                            'name' => 'String',
                                            'kind' => 'SCALAR',
                                            'description' => '',
                                            'ofType' => null,
                                        ],
                                    ]
                                ], [
                                    'name' => 'name_not_in',
                                    'description' => '',
                                    'type' => [
                                        'name' => null,
                                        'description' => '',
                                        'kind' => 'LIST',
                                        'ofType' => [
                                            'name' => null,
                                            'description' => '',
                                            'kind' => 'NOT_NULL',
                                            'ofType' => [
                                                'name' => 'String',
                                                'description' => '',
                                                'kind' => 'SCALAR',
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ],
                        'ofType' => null,
                    ]
                ]
            ],
        ];

        return [
            'SimpleObjectCase' => [$simpleObjectSchema, ['SimpleObjectQueryObject']],
            'ComplexObjectCase' => [$complexObjectSchema, ['ComplexObjectQueryObject']],
            'WithEnumCase' => [$withEnumsObjectSchema, ['WithEnumQueryObject', $orderingObjectName . 'EnumObject']],
            'WithInputObjectCase' => [$withInputObjectSchema, ['WithInputQueryObject', $inputObjectName . 'InputObject']],
        ];
    }
}
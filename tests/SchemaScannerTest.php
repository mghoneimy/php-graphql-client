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
     * @covers SchemaScanner::setWriteDir
     * @covers SchemaScanner::getWriteDir
     */
    public function testSetWriteDirectory()
    {
        $schemaScanner = new SchemaScanner();
        $this->assertStringEndsWith('/graphql-client/schema_object', $schemaScanner->getWriteDir());
    }

    /**
     * @param array  $schemaTypes
     * @param string $expectedFileName
     *
     * @dataProvider schemaStringProvider
     * 
     * @covers \GraphQL\SchemaGenerator\SchemaScanner::generateSchemaObjects
     */
    public function testSchemaTypesReading(array $schemaTypes, $expectedFileName)
    {
        $schemaScanner = new SchemaScanner();
        $schemaScanner->generateSchemaObjects($schemaTypes, static::getGeneratedFilesDir());

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$expectedFileName.php",
            static::getGeneratedFilesDir() . "/$expectedFileName.php"
        );
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
                    ],
                    [
                        'name' => 'name',
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

        return [
            'SimpleObjectCase' => [$simpleObjectSchema, 'SimpleObjectQueryObject'],
            'ComplexObjectCase' => [$complexObjectSchema, 'ComplexObjectQueryObject'],
        ];
    }
}
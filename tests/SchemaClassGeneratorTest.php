<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GraphQL\Enumeration\FieldTypeKindEnum;
use GraphQL\SchemaGenerator\SchemaClassGenerator;


class SchemaClassGeneratorTest extends CodeFileTestCase
{
    private const TEST_API_URL = 'https://graphql-pokemon.now.sh/';

    /**
     * @var TransparentSchemaClassGenerator
     */
    protected $classGenerator;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->classGenerator = new TransparentSchemaClassGenerator(
            new Client(static::TEST_API_URL),
            static::getGeneratedFilesDir()
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::__construct
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::setWriteDir
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::getWriteDir
     */
    public function testSetWriteDirectory()
    {
        $this->classGenerator = new SchemaClassGenerator(
            new Client(static::TEST_API_URL)
        );
        $this->assertStringEndsWith('/php-graphql-client/schema_object', $this->classGenerator->getWriteDir());

        $this->classGenerator = new SchemaClassGenerator(
            new Client(static::TEST_API_URL),
            static::getGeneratedFilesDir()
        );
        $this->assertStringEndsWith('/tests/files_generated', $this->classGenerator->getWriteDir());
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::getTypeInfo
     */
    public function testGetTypeInfo()
    {
        $dataArray = [
            'type' => [
                'name' => 'String',
                'kind' => FieldTypeKindEnum::SCALAR,
                'ofType' => null,
            ]
        ];

        $typeInfo = $this->classGenerator->getTypeInfo($dataArray);
        $this->assertEquals(
            [
                'String',
                FieldTypeKindEnum::SCALAR,
                []
            ],
            $typeInfo
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::getTypeInfo
     */
    public function testGetTypeInfoForMultiLevels()
    {
        $dataArray = [
            'type' => [
                'name' => null,
                'kind' => FieldTypeKindEnum::LIST,
                'ofType' => [
                    'name' => null,
                    'kind' => FieldTypeKindEnum::NON_NULL,
                    'ofType' => [
                        'name' => 'WrappedObject',
                        'kind' => FieldTypeKindEnum::OBJECT,
                        'ofType' => null
                    ]
                ]
            ]
        ];

        $typeInfo = $this->classGenerator->getTypeInfo($dataArray);
        $this->assertEquals(
            [
                'WrappedObject',
                FieldTypeKindEnum::OBJECT,
                [FieldTypeKindEnum::LIST, FieldTypeKindEnum::NON_NULL]
            ],
            $typeInfo
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::getTypeInfo
     */
    public function testCrossNestingLimitForGetTypeInfo()
    {
        $dataArray = [
            'type' => [
                'name' => null,
                'kind' => FieldTypeKindEnum::NON_NULL,
                'ofType' => [
                    'name' => null,
                    'kind' => FieldTypeKindEnum::LIST,
                    'ofType' => [
                        'name' => null,
                        'kind' => FieldTypeKindEnum::NON_NULL,
                        'ofType' => [
                            'name' => 'WrappedObject',
                            'kind' => 'OBJECT'
                        ]
                    ]
                ]
            ]
        ];

        $this->expectExceptionMessage('Reached the limit of nesting in type info');
        $this->classGenerator->getTypeInfo($dataArray);
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateEnumObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateEnumObjectFromArray
     */
    public function testGenerateEnumObject()
    {
        $objectName = 'WithMultipleConstants';
        $enumArray = [
            'name' => $objectName,
            'kind' => FieldTypeKindEnum::ENUM_OBJECT,
            'enumValues' => [
                [
                    'name' => 'some_value',
                    'description' => null,
                ], [
                    'name' => 'another_value',
                    'description' => null,
                ], [
                    'name' => 'oneMoreValue',
                    'description' => null,
                ],
            ]
        ];
        $objectName .= 'EnumObject';

        $this->classGenerator->generateEnumObjectFromArray($enumArray);
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/enum_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObjectFromArray
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateInputObjectWithScalarValues()
    {
        $objectName = 'WithMultipleScalarValues';
        $enumArray = [
            'name' => $objectName,
            'kind' => FieldTypeKindEnum::INPUT_OBJECT,
            'inputFields' => [
                [
                    'name' => 'valOne',
                    'description' => null,
                    'defaultValue' => null,
                    'type' => [
                        'name' => 'String',
                        'kind' => FieldTypeKindEnum::SCALAR,
                        'description' => null,
                        'ofType' => null,
                    ],
                ], [
                    'name' => 'val_two',
                    'description' => null,
                    'defaultValue' => null,
                    'type' => [
                        'name' => 'String',
                        'kind' => FieldTypeKindEnum::SCALAR,
                        'description' => null,
                        'ofType' => null,
                    ],
                ],
            ]
        ];
        $objectName .= 'InputObject';

        $this->classGenerator->generateInputObjectFromArray($enumArray);
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/input_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObjectFromArray
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateInputObjectWithListValues()
    {
        $objectName = 'WithMultipleListValues';
        $enumArray = [
            'name' => $objectName,
            'kind' => FieldTypeKindEnum::INPUT_OBJECT,
            'inputFields' => [
                [
                    'name' => 'listOne',
                    'description' => null,
                    'defaultValue' => null,
                    'type' => [
                        'name' => null,
                        'kind' => FieldTypeKindEnum::LIST,
                        'description' => null,
                        'ofType' => [
                            'name' => 'String',
                            'kind' => FieldTypeKindEnum::SCALAR,
                            'description' => null,
                            'ofType' => null,
                        ],
                    ],
                ], [
                    'name' => 'list_two',
                    'description' => null,
                    'defaultValue' => null,
                    'type' => [
                        'name' => null,
                        'kind' => FieldTypeKindEnum::NON_NULL,
                        'description' => null,
                        'ofType' => [
                            'name' => null,
                            'kind' => FieldTypeKindEnum::LIST,
                            'description' => null,
                            'ofType' => [
                                'name' => 'Integer',
                                'kind' => FieldTypeKindEnum::SCALAR,
                                'description' => null,
                                'ofType' => null,
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $objectName .= 'InputObject';

        $this->classGenerator->generateInputObjectFromArray($enumArray);
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/input_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    ///**
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
    // */
    //public function testGenerateInputObjectWithNestedObjectValues()
    //{
    //    $objectName = 'WithMultipleInputObjectValues';
    //    $enumArray = [
    //        'name' => $objectName,
    //        'kind' => FieldTypeKindEnum::INPUT_OBJECT,
    //        'inputFields' => [
    //            [
    //                'name' => 'inputObject',
    //                'description' => null,
    //                'defaultValue' => null,
    //                'type' => [
    //                    'name' => 'WithListValueInputObject',
    //                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
    //                    'description' => null,
    //                    'ofType' => null,
    //                ],
    //            ], [
    //                'name' => 'inputObjectTwo',
    //                'description' => null,
    //                'defaultValue' => null,
    //                'type' => [
    //                    'name' => '_TestFilterInputObject',
    //                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
    //                    'description' => null,
    //                ],
    //            ],
    //        ]
    //    ];
    //    $objectName .= 'InputObject';
    //
    //    $this->classGenerator->generateInputObjectFromArray($enumArray);
    //    $this->assertFileEquals(
    //        static::getExpectedFilesDir() . "/input_objects/$objectName.php",
    //        static::getGeneratedFilesDir() . "/$objectName.php"
    //    );
    //}

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateArgumentsObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateArgumentsObjectWithScalarArgs()
    {
        $objectName = 'WithMultipleScalarArgs';
        $argsArray  = [
            [
                'name' => 'scalarProperty',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => 'String',
                    'kind' => FieldTypeKindEnum::SCALAR,
                    'description' => null,
                    'ofType' => null,
                ]
            ], [
                'name' => 'another_scalar_property',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => 'String',
                    'kind' => FieldTypeKindEnum::SCALAR,
                    'description' => null,
                    'ofType' => null,
                ]
            ]
        ];
        $this->classGenerator->generateArgumentsObject('WithMultipleScalarArgs', $argsArray);

        $objectName .= 'ArgumentsObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/arguments_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateArgumentsObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateArgumentsObjectWithListArgs()
    {
        $objectName = 'WithMultipleListArgs';
        $argsArray  = [
            [
                'name' => 'listProperty',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => null,
                    'kind' => FieldTypeKindEnum::LIST,
                    'description' => null,
                    'ofType' => [
                        'name' => 'String',
                        'kind' => FieldTypeKindEnum::SCALAR,
                        'description' => null,
                        'ofType' => null,
                    ]
                ]
            ], [
                'name' => 'another_list_property',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => null,
                    'kind' => FieldTypeKindEnum::NON_NULL,
                    'description' => null,
                    'ofType' => [
                        'name' => null,
                        'kind' => FieldTypeKindEnum::LIST,
                        'description' => null,
                        'ofType' => [
                            'name' => 'Integer',
                            'kind' => FieldTypeKindEnum::SCALAR,
                            'description' => null,
                            'ofType' => null,
                        ]
                    ]
                ]
            ]
        ];
        $this->classGenerator->generateArgumentsObject($objectName, $argsArray);

        $objectName .= 'ArgumentsObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/arguments_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    ///**
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateArgumentsObject
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
    // */
    //public function testGenerateArgumentsObjectWithNestedObjectArgs()
    //{
    //    $objectName = 'WithMultipleInputObjectArgs';
    //    $argsArray  = [
    //        [
    //            'name' => 'objectProperty',
    //            'description' => null,
    //            'defaultValue' => null,
    //            'type' => [
    //                'name' => 'SomeInputObject',
    //                'kind' => FieldTypeKindEnum::INPUT_OBJECT,
    //                'description' => null,
    //                'ofType' => null,
    //            ],
    //        ], [
    //            'name' => 'another_object_property',
    //            'description' => null,
    //            'defaultValue' => null,
    //            'type' => [
    //                'name' => 'AnotherInputObject',
    //                'kind' => FieldTypeKindEnum::INPUT_OBJECT,
    //                'description' => null,
    //            ],
    //        ],
    //    ];
    //    $this->classGenerator->generateArgumentsObject($objectName, $argsArray);
    //
    //    $objectName .= 'ArgumentsObject';
    //    $this->assertFileEquals(
    //        static::getExpectedFilesDir() . "/arguments_objects/$objectName.php",
    //        static::getGeneratedFilesDir() . "/$objectName.php"
    //    );
    //}

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObjectFromArray
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::appendQueryObjectFields
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateQueryObjectWithScalarFields()
    {
        $objectName  = 'MultipleSimpleSelectors';
        $objectArray = [
            'name' => $objectName,
            'kind' => FieldTypeKindEnum::OBJECT,
            'fields' => [
                [
                    'name' => 'first_name',
                    'description' => null,
                    'type' => [
                        'name' => 'String',
                        'kind' => FieldTypeKindEnum::SCALAR,
                        'description' => null,
                        'ofType' => null,
                    ],
                    'args' => null,
                ], [
                    'name' => 'last_name',
                    'description' => null,
                    'type' => [
                        'name' => 'String',
                        'kind' => FieldTypeKindEnum::SCALAR,
                        'description' => null,
                        'ofType' => null,
                    ],
                    'args' => null,
                ]
            ]
        ];
        $objectName .= 'QueryObject';

        $this->classGenerator->generateQueryObjectFromArray($objectArray);
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/query_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    ///**
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObject
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObjectFromArray
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::appendQueryObjectFields
    // * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
    // */
    //public function testGenerateQueryObjectWithObjectFields()
    //{
    //    $objectName  = 'MultipleObjectSelectors';
    //    $objectArray = [
    //        'name' => $objectName,
    //        'kind' => FieldTypeKindEnum::OBJECT,
    //        'fields' => [
    //            [
    //                'name' => '',
    //                'description' => null,
    //                'type' => [
    //
    //                ],
    //                'args' => [
    //                    'name' => '',
    //                    'description' => null,
    //                    'defaultValue' => null,
    //                    'type' => [
    //
    //                    ],
    //                ],
    //            ], [
    //
    //            ],
    //        ]
    //    ];
    //    $objectName .= 'QueryObject';
    //
    //    $this->classGenerator->generateQueryObjectFromArray($objectArray);
    //    $this->assertFileEquals(
    //        static::getExpectedFilesDir() . "/query_objects/$objectName.php",
    //        static::getGeneratedFilesDir() . "/$objectName.php"
    //    );
    //}

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateRootQueryObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateRootQueryObjectFromArray
     */
    public function testGenerateRootObject()
    {
        $objectArray = [
            'name' => 'Query',
            'kind' => FieldTypeKindEnum::OBJECT,
            'description' => null,
            'fields' => []
        ];
        $this->classGenerator->generateRootQueryObjectFromArray($objectArray);

        $objectName = 'RootQueryObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/query_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}

class TransparentSchemaClassGenerator extends SchemaClassGenerator
{
    public function generateRootQueryObjectFromArray(array $objectArray): bool
    {
        return parent::generateRootQueryObjectFromArray($objectArray);
    }

    public function generateQueryObjectFromArray(array $objectArray): bool
    {
        return parent::generateQueryObjectFromArray($objectArray);
    }

    public function generateInputObjectFromArray(array $objectArray): bool
    {
        return parent::generateInputObjectFromArray($objectArray);
    }

    public function generateEnumObjectFromArray(array $objectArray): bool
    {
        return parent::generateEnumObjectFromArray($objectArray);
    }

    public function generateArgumentsObject(string $argsObjectName, array $arguments): bool
    {
        return parent::generateArgumentsObject($argsObjectName, $arguments);
    }

    public function getTypeInfo(array $dataArray): array
    {
        return parent::getTypeInfo($dataArray);
    }
}
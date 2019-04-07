<?php

namespace GraphQL\Tests;

use GraphQL\Client;
use GraphQL\Enumeration\FieldTypeKindEnum;
use GraphQL\SchemaGenerator\SchemaClassGenerator;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class SchemaClassGeneratorTest extends CodeFileTestCase
{
    private const TEST_API_URL = 'https://graphql-pokemon.now.sh/';

    /**
     * @var TransparentSchemaClassGenerator
     */
    protected $classGenerator;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->mockHandler    = new MockHandler();
        $this->classGenerator = new TransparentSchemaClassGenerator(
            new MockClient(static::TEST_API_URL, $this->mockHandler),
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
     */
    public function testGenerateEnumObject()
    {
        $objectName = 'WithMultipleConstants';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
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
                ]
            ]
        ])));
        $this->classGenerator->generateEnumObject($objectName);

        $objectName .= 'EnumObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/enum_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateInputObjectWithScalarValues()
    {
        $objectName = 'WithMultipleScalarValues';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
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
                ]
            ]
        ])));
        $this->classGenerator->generateInputObject($objectName);

        $objectName .= 'InputObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/input_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateInputObjectWithListValues()
    {
        $objectName = 'WithMultipleListValues';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
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
                ]
            ]
        ])));
        $this->classGenerator->generateInputObject($objectName);

        $objectName .= 'InputObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/input_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateInputObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateInputObjectWithNestedObjectValues()
    {
        $objectName = 'WithMultipleInputObjectValues';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => $objectName,
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'inputFields' => [
                        [
                            'name' => 'inputObject',
                            'description' => null,
                            'defaultValue' => null,
                            'type' => [
                                'name' => 'WithListValue',
                                'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                                'description' => null,
                                'ofType' => null,
                            ],
                        ], [
                            'name' => 'inputObjectTwo',
                            'description' => null,
                            'defaultValue' => null,
                            'type' => [
                                'name' => '_TestFilter',
                                'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                                'description' => null,
                                'ofType' => null,
                            ],
                        ],
                    ]
                ]
            ]
        ])));
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'WithListValue',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'inputFields' => []
                ]
            ]
        ])));
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => '_TestFilter',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'inputFields' => []
                ]
            ]
        ])));
        $this->classGenerator->generateInputObject($objectName);

        $objectName .= 'InputObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/input_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

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
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'Some',
                    'kind' => FieldTypeKindEnum::ENUM_OBJECT,
                    'enumValues' => [
                        [
                            'name' => 'some_value',
                            'description' => null,
                        ]
                    ]
                ]
            ]
        ])));
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
                        'name' => 'Some',
                        'kind' => FieldTypeKindEnum::ENUM_OBJECT,
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

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateArgumentsObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateArgumentsObjectWithInputObjectArgs()
    {
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'Some',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'inputFields' => []
                ]
            ]
        ])));
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'Another',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'inputFields' => []
                ]
            ]
        ])));

        $objectName = 'WithMultipleInputObjectArgs';
        $argsArray  = [
            [
                'name' => 'objectProperty',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => 'Some',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'description' => null,
                    'ofType' => null,
                ],
            ], [
                'name' => 'another_object_property',
                'description' => null,
                'defaultValue' => null,
                'type' => [
                    'name' => 'Another',
                    'kind' => FieldTypeKindEnum::INPUT_OBJECT,
                    'description' => null,
                    'ofType' => null,
                ],
            ],
        ];
        $this->classGenerator->generateArgumentsObject($objectName, $argsArray);

        $objectName .= 'ArgumentsObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/arguments_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::appendQueryObjectFields
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateQueryObjectWithScalarFields()
    {
        $objectName  = 'MultipleSimpleSelectors';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
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
                ]
            ]
        ])));
        $this->classGenerator->generateQueryObject($objectName);

        $objectName .= 'QueryObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/query_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateQueryObject
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::appendQueryObjectFields
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateQueryObjectWithObjectFields()
    {
        $objectName  = 'MultipleObjectSelectors';
        // Add mock responses
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => $objectName,
                    'kind' => FieldTypeKindEnum::OBJECT,
                    'fields' => [
                        [
                            'name' => 'right_objects',
                            'description' => null,
                            'type' => [
                                'name' => null,
                                'kind' => FieldTypeKindEnum::LIST,
                                'description' => null,
                                'ofType' => [
                                    'name' => 'Right',
                                    'kind' => FieldTypeKindEnum::OBJECT,
                                    'description' => null,
                                    'ofType' => null,
                                ]
                            ],
                            'args' => null,
                        ], [
                            'name' => 'left_objects',
                            'description' => null,
                            'type' => [
                                'name' => null,
                                'kind' => FieldTypeKindEnum::LIST,
                                'description' => null,
                                'ofType' => [
                                    'name' => 'Left',
                                    'kind' => FieldTypeKindEnum::OBJECT,
                                    'description' => null,
                                    'ofType' => null,
                                ]
                            ],
                            'args' => null,
                        ],
                    ]
                ]
            ]
        ])));
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'Right',
                    'kind' => FieldTypeKindEnum::OBJECT,
                    'fields' => []
                ]
            ]
        ])));
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__type' => [
                    'name' => 'Left',
                    'kind' => FieldTypeKindEnum::OBJECT,
                    'fields' => []
                ]
            ]
        ])));
        $objectName .= 'QueryObject';

        $this->classGenerator->generateQueryObject($objectName);
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/query_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateRootQueryObject
     */
    public function testGenerateRootObject()
    {
        $this->mockHandler->append(new Response(200, [], json_encode([
            'data' => [
                '__schema' => [
                    'queryType' => [
                        'name' => 'Query',
                        'kind' => FieldTypeKindEnum::OBJECT,
                        'description' => null,
                        'fields' => []
                    ]
                ]
            ]
        ])));
        $this->classGenerator->generateRootQueryObject();

        $objectName = 'RootQueryObject';
        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/query_objects/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\SchemaClassGenerator::generateObject
     */
    public function testGenerateObjectWithUnregisteredKind()
    {
        $this->expectExceptionMessage('Unsupported object type');
        $this->classGenerator->generateObject('someNae', 'someKind');
    }
}

class TransparentSchemaClassGenerator extends SchemaClassGenerator
{
    public function generateRootQueryObject(): bool
    {
        return parent::generateRootQueryObject();
    }

    public function generateQueryObject(string $objectName): bool
    {
        return parent::generateQueryObject($objectName);
    }

    public function generateEnumObject(string $objectName): bool
    {
        return parent::generateEnumObject($objectName);
    }

    public function generateInputObject(string $objectName): bool
    {
        return parent::generateInputObject($objectName);
    }

    public function generateObject(string $objectName, string $objectKind): bool
    {
        return parent::generateObject($objectName, $objectKind);
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
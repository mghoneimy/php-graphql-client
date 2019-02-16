<?php

use GraphQL\SchemaManager\SchemaScanner;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/9/19
 * Time: 4:19 PM
 */

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
        $this->assertStringEndsWith('/graphql-client/schema_object', SchemaScanner::getWriteDir());
    }

    /**
     * @param array $schemaTypes
     * @param       $expectedFileNames
     *
     * @dataProvider schemaStringProvider
     * 
     * @covers \GraphQL\SchemaManager\SchemaScanner::generateSchemaObjects 
     */
    public function testSchemaTypesReading(array $schemaTypes, array $expectedFileNames)
    {
        SchemaScanner::generateSchemaObjects($schemaTypes, static::getGeneratedFilesDir());

        foreach ($expectedFileNames as $fileName) {
            $this->assertFileEquals(
                static::getExpectedFilesDir() . "/$fileName.php",
                static::getGeneratedFilesDir() . "/$fileName.php"
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
                ]
            ]
        ];
        
        $complexObjectSchema = [
            [
                'name' => 'ComplexObject',
                'kind' => 'OBJECT',
                'description' => 'another description',
                'fields' =>[
                    [
                        'name' => '_id',
                        'description' => 'complex object id',
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
                        'description' => 'complex object creation date',
                        'type' => [
                            'name' => 'String',
                            'kind' => 'SCALAR',
                            'ofType' => null
                        ]
                    ]
                ]
            ]
        ];

        return [
            'SimpleObjectCase' => [$simpleObjectSchema, ['SimpleObjectTrait', 'SimpleObjectQueryObject']],
            'ComplexObjectCase' => [$complexObjectSchema, ['ComplexObjectTrait', 'ComplexObjectQueryObject']],
        ];
    }
}
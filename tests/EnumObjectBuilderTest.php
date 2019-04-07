<?php

namespace GraphQL\Tests;

use GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/23/19
 * Time: 4:22 PM
 */

class EnumObjectBuilderTest extends CodeFileTestCase
{
    private const TESTING_NAMESPACE = 'GraphQL\\Tests\\SchemaObject';

    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/enum_objects';
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder::build
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder::__construct
     */
    public function testBuildEmptyEnum()
    {
        $objectName = 'Empty';
        $enumBuilder = new EnumObjectBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'EnumObject';
        $enumBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testBuildEmptyEnum
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder::addEnumValue
     */
    public function testAddValue()
    {
        $objectName = 'WithConstant';
        $enumBuilder = new EnumObjectBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'EnumObject';
        $enumBuilder->addEnumValue('fixed_value');
        $enumBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testBuildEmptyEnum
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder::addEnumValue
     */
    public function testAddMultipleValues()
    {
        $objectName = 'WithMultipleConstants';
        $enumBuilder = new EnumObjectBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'EnumObject';
        $enumBuilder->addEnumValue('some_value');
        $enumBuilder->addEnumValue('another_value');
        $enumBuilder->addEnumValue('oneMoreValue');
        $enumBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}
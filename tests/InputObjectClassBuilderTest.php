<?php

namespace GraphQL\Tests;

use GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder;

/**
 * Class InputObjectClassBuilderTest
 */
class InputObjectClassBuilderTest extends CodeFileTestCase
{
    private const TESTING_NAMESPACE = 'GraphQL\\Tests\\SchemaObject';

    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/input_objects';
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addScalarValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addScalarSetter
     */
    public function testAddScalarValue()
    {
        $objectName = 'WithScalarValue';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addScalarValue('valOne');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addScalarValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addScalarSetter
     */
    public function testAddMultipleScalarValues()
    {
        $objectName = 'WithMultipleScalarValues';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addScalarValue('valOne');
        $classBuilder->addScalarValue('val_two');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addListValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addListSetter
     */
    public function testAddListValue()
    {
        $objectName = 'WithListValue';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addListValue('listOne', '');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addListValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addListSetter
     */
    public function testAddMultipleListValues()
    {
        $objectName = 'WithMultipleListValues';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addListValue('listOne', '');
        $classBuilder->addListValue('list_two', '');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addInputObjectValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addObjectSetter
     */
    public function testAddInputObjectValue()
    {
        $objectName = 'WithInputObjectValue';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addInputObjectValue('inputObject', 'WithListValue');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addInputObjectValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     *
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addProperty
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\ObjectClassBuilder::addObjectSetter
     */
    public function testAddMultipleInputObjectValues()
    {
        $objectName = 'WithMultipleInputObjectValues';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addInputObjectValue('inputObject', 'WithListValue');
        $classBuilder->addInputObjectValue('inputObjectTwo', '_TestFilter');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testInputObjectIntegration()
    {
        $objectName = '_TestFilter';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName, static::TESTING_NAMESPACE);
        $objectName .= 'InputObject';
        $classBuilder->addScalarValue('first_name');
        $classBuilder->addScalarValue('lastName');
        $classBuilder->addListValue('ids', '');
        $classBuilder->addInputObjectValue('testFilter', '_TestFilter');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}
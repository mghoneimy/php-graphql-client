<?php

use GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder;

/**
 * Class InputObjectClassBuilderTest
 */
class InputObjectClassBuilderTest extends CodeFileTestCase
{
    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/input_objects';
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addScalarValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testAddScalarValue()
    {
        $objectName = 'WithScalarValue';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'InputObject';
        $classBuilder->addScalarValue('valOne');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addScalarValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testAddMultipleScalarValues()
    {
        $objectName = 'WithMultipleScalarValues';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
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
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addListValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testAddListValue()
    {
        $objectName = 'WithListValue';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'InputObject';
        $classBuilder->addListValue('listOne', '');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::addListValue
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testAddMultipleListValues()
    {
        $objectName = 'WithMultipleListValues';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
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
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder::build
     */
    public function testInputObjectIntegration()
    {
        $objectName = '_TestFilter';
        $classBuilder = new InputObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'InputObject';
        $classBuilder->addScalarValue('first_name');
        $classBuilder->addScalarValue('lastName');
        $classBuilder->addListValue('ids', '');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}
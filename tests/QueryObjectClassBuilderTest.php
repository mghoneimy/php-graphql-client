<?php

use GraphQL\SchemaManager\CodeGenerator\QueryObjectClassBuilder;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/9/19
 * Time: 9:22 PM
 */

class QueryObjectClassBuilderTest extends CodeFileTestCase
{
    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/query_object_classes';
    }

    /**
     * @covers QueryObjectClassBuilder::addSetter
     */
    public function testAddPropertySetter()
    {
        $objectName = 'WithSetter';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addSetter('name', 'Name');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testAddPropertySetter
     *
     * @covers QueryObjectClassBuilder::addSetter
     */
    public function testAddMultiplePropertySetters()
    {
        $objectName = 'WithMultipleSetters';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addSetter('last_name', 'LastName');
        $classBuilder->addSetter('first_name', 'FirstName');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers QueryObjectClassBuilder::addSimpleSelector
     */
    public function testAddSimpleSelector()
    {
        $objectName = 'SimpleSelector';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addSimpleSelector('name', 'Name');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testAddSimpleSelector
     * 
     * @covers QueryObjectClassBuilder::addSimpleSelector
     */
    public function testAddMultipleSimpleSelectors()
    {
        $objectName = 'MultipleSimpleSelectors';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addSimpleSelector('first_name', 'FirstName');
        $classBuilder->addSimpleSelector('last_name', 'LastName');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @covers QueryObjectClassBuilder::addObjectSelector
     */
    public function testAddObjectSelector()
    {
        $objectName = 'ObjectSelector';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addObjectSelector('others', 'Others', 'Other');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testAddObjectSelector
     * 
     * @covers QueryObjectClassBuilder::addObjectSelector
     */
    public function testAddMultipleObjectSelectors()
    {
        $objectName = 'MultipleObjectSelectors';
        $classBuilder = new QueryObjectClassBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'QueryObject';
        $classBuilder->addObjectSelector('right_objects', 'RightObjects', 'RightObject');
        $classBuilder->addObjectSelector('left_objects', 'LeftObjects', 'LeftObject');
        $classBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}
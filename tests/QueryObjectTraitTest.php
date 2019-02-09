<?php

use GraphQL\SchemaManager\CodeGenerator\QueryObjectTraitBuilder;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/9/19
 * Time: 10:49 PM
 */

class QueryObjectTraitTest extends CodeFileTestCase
{
    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/query_object_traits';
    }

    /**
     * @covers QueryObjectTraitBuilder::addProperty
     */
    public function testAddProperty()
    {
        $objectName = 'WithProperty';
        $traitBuilder = new QueryObjectTraitBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'Trait';
        $traitBuilder->addProperty('property');
        $traitBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }

    /**
     * @depends testAddProperty
     *
     * @covers QueryObjectTraitBuilder::addProperty
     */
    public function testAddProperties()
    {
        $objectName = 'WithMultipleProperties';
        $traitBuilder = new QueryObjectTraitBuilder(static::getGeneratedFilesDir(), $objectName);
        $objectName .= 'Trait';
        $traitBuilder->addProperty('first_property');
        $traitBuilder->addProperty('secondProperty');
        $traitBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$objectName.php",
            static::getGeneratedFilesDir() . "/$objectName.php"
        );
    }
}
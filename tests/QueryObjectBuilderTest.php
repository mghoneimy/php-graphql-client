<?php

use GraphQL\SchemaManager\CodeGenerator\QueryObjectBuilder;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/9/19
 * Time: 11:06 PM
 */

class QueryObjectBuilderTest extends CodeFileTestCase
{
    /**
     * @return string
     */
    protected static function getExpectedFilesDir()
    {
        return parent::getExpectedFilesDir() . '/query_objects';
    }

    /**
     * @covers \GraphQL\SchemaManager\CodeGenerator\QueryObjectBuilder
     */
    public function testBuildQueryObject()
    {
        $objectName = 'Test';
        $objectBuilder = new QueryObjectBuilder(static::getGeneratedFilesDir(), $objectName);
        $className = $objectName . 'QueryObject';
        $objectBuilder->addScalarArgument('property_one');
        $objectBuilder->addScalarField('property_one');
        $objectBuilder->addScalarArgument('propertyTwo');
        $objectBuilder->addScalarField('propertyTwo');
        $objectBuilder->addObjectField('other_objects', 'OtherObject');
        $objectBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$className.php",
            static::getGeneratedFilesDir() . "/$className.php"
        );
    }
}
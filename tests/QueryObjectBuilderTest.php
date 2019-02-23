<?php

use GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder;

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
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder
     */
    public function testBuildQueryObject()
    {
        $objectName = 'Test';
        $objectBuilder = new QueryObjectBuilder(static::getGeneratedFilesDir(), $objectName);
        $className = $objectName . 'QueryObject';
        $objectBuilder->addScalarField('property_one');
        $objectBuilder->addScalarField('propertyTwo');
        $objectBuilder->addObjectField('other_objects', 'OtherObject');
        $objectBuilder->addScalarArgument('property_one');
        $objectBuilder->addScalarArgument('propertyTwo');
        $objectBuilder->addListArgument('propertyTwos', 'PropertyTwo');
        $objectBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$className.php",
            static::getGeneratedFilesDir() . "/$className.php"
        );
    }
}
<?php

namespace GraphQL\Tests;

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
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::__construct
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::addScalarField
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::addObjectField
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::addScalarArgument
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::addListArgument
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::addInputObjectArgument
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::getUpperCamelCase
     * @covers \GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder::build
     */
    public function testBuildQueryObject()
    {
        $objectName = 'Test';
        $objectBuilder = new QueryObjectBuilder(static::getGeneratedFilesDir(), $objectName);
        $className = $objectName . 'QueryObject';
        $objectBuilder->addScalarField('property_one');
        $objectBuilder->addScalarField('propertyTwo');
        $objectBuilder->addScalarField('propertyWithoutSetter');
        $objectBuilder->addObjectField('other_objects', 'OtherObject');
        $objectBuilder->addScalarArgument('property_one');
        $objectBuilder->addScalarArgument('propertyTwo');
        $objectBuilder->addListArgument('propertyTwos', 'PropertyTwo');
        $objectBuilder->addInputObjectArgument('filterBy', '_TestFilter');
        $objectBuilder->build();

        $this->assertFileEquals(
            static::getExpectedFilesDir() . "/$className.php",
            static::getGeneratedFilesDir() . "/$className.php"
        );
    }
}
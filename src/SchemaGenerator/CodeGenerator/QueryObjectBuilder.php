<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

/**
 * Class QueryObjectBuilder
 *
 * @package GraphQL\SchemaManager\CodeGenerator
 */
class QueryObjectBuilder
{
    /**
     * @var QueryObjectClassBuilder
     */
    protected $classBuilder;

    /**
     * SchemaObjectBuilder constructor.
     *
     * @param $writeDir
     * @param $objectName
     */
    public function __construct($writeDir, $objectName)
    {
        $this->classBuilder = new QueryObjectClassBuilder($writeDir, $objectName);
    }

    /**
     * @param $argumentName
     */
    public function addScalarArgument($argumentName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addSimpleSetter($argumentName, $upperCamelCaseArg);
    }

    /**
     * @param $argumentName
     * @param $typeName
     */
    public function addListArgument($argumentName, $typeName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addListSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param $argumentName
     * @param $typeName
     */
    public function addInputObjectArgument($argumentName, $typeName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addInputObjectSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param $fieldName
     */
    public function addScalarField($fieldName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->classBuilder->addSimpleSelector($fieldName, $upperCamelCaseProp);
    }

    /**
     * @param $fieldName
     * @param $typeName
     */
    public function addObjectField($fieldName, $typeName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->classBuilder->addObjectSelector($fieldName, $upperCamelCaseProp, $typeName);
    }

    /**
     * @param $propertyName
     *
     * @return string
     */
    protected function getUpperCamelCase($propertyName)
    {
        if (strpos($propertyName, '_') === false) {
            return ucfirst($propertyName);
        } else {
            return str_replace('_', '', ucwords($propertyName, '_'));
        }
    }

    /**
     *
     */
    public function build()
    {
        $this->classBuilder->build();
    }
}
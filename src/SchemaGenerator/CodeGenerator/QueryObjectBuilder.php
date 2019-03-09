<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

/**
 * Class QueryObjectBuilder
 *
 * @package GraphQL\SchemaManager\CodeGenerator
 */
class QueryObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var QueryObjectClassBuilder
     */
    protected $classBuilder;

    /**
     * SchemaObjectBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     */
    public function __construct(string $writeDir, string $objectName)
    {
        $this->classBuilder = new QueryObjectClassBuilder($writeDir, $objectName);
    }

    /**
     * @param string $argumentName
     */
    public function addScalarArgument(string $argumentName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addSimpleSetter($argumentName, $upperCamelCaseArg);
    }

    /**
     * @param string string $argumentName
     * @param string string $typeName
     */
    public function addListArgument(string $argumentName, string $typeName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addListSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param string $argumentName
     * @param string $typeName
     */
    public function addInputObjectArgument(string $argumentName, string $typeName)
    {
        $typeName .= 'InputObject';
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->classBuilder->addProperty($argumentName);
        $this->classBuilder->addInputObjectSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param string $fieldName
     */
    public function addScalarField(string $fieldName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->classBuilder->addSimpleSelector($fieldName, $upperCamelCaseProp);
    }

    /**
     * @param string $fieldName
     * @param string $typeName
     */
    public function addObjectField(string $fieldName, string $typeName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->classBuilder->addObjectSelector($fieldName, $upperCamelCaseProp, $typeName);
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    protected function getUpperCamelCase(string $propertyName)
    {
        if (strpos($propertyName, '_') === false) {
            return ucfirst($propertyName);
        } else {
            return str_replace('_', '', ucwords($propertyName, '_'));
        }
    }

    /**
     * @inheritdoc
     */
    public function build(): void
    {
        $this->classBuilder->build();
    }
}
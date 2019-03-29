<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;

/**
 * Class InputObjectClassBuilder
 *
 * @package GraphQL\SchemaGenerator\CodeGenerator
 */
class InputObjectClassBuilder extends ObjectClassBuilder
{
    /**
     * SchemaObjectBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     */
    public function __construct(string $writeDir, string $objectName)
    {
        $className = $objectName . 'InputObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('InputObject');
    }

    /**
     * @param string $argumentName
     */
    public function addScalarValue(string $argumentName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addSimpleSetter($argumentName, $upperCamelCaseArg);
    }

    /**
     * @param string $argumentName
     * @param string $typeName
     */
    public function addListValue(string $argumentName, string $typeName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addListSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param string $argumentName
     * @param string $typeName
     */
    public function addInputObjectValue(string $argumentName, string $typeName)
    {
        $typeName .= 'InputObject';
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addInputObjectSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @return void
     */
    public function build()
    {
        $this->classFile->writeFile();
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    private function getUpperCamelCase(string $propertyName): string
    {
        if (strpos($propertyName, '_') === false) {
            return ucfirst($propertyName);
        } else {
            return str_replace('_', '', ucwords($propertyName, '_'));
        }
    }
}
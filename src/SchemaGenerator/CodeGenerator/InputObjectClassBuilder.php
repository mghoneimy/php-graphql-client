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
     * @param $writeDir
     * @param $objectName
     */
    public function __construct($writeDir, $objectName)
    {
        $className = $objectName . 'InputObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('InputObject');
    }

    /**
     * @param $argumentName
     */
    public function addScalarValue($argumentName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addSimpleSetter($argumentName, $upperCamelCaseArg);
    }

    /**
     * @param $argumentName
     * @param $typeName
     */
    public function addListValue($argumentName, $typeName)
    {
        $upperCamelCaseArg = $this->getUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addListSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @return void
     */
    public function build()
    {
        $this->classFile->writeFile();
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
}
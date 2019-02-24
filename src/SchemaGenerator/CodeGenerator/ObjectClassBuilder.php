<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;

/**
 * Class ObjectClassBuilder
 *
 * @package GraphQL\SchemaGenerator\CodeGenerator
 */
abstract class ObjectClassBuilder implements ObjectBuilderInterface
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * @param string $propertyName
     */
    public function addProperty($propertyName)
    {
        $this->classFile->addProperty($propertyName);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     */
    public function addSimpleSetter($propertyName, $upperCamelName)
    {
        $lowerCamelName = lcfirst($upperCamelName);
        $method = "public function set$upperCamelName($$lowerCamelName)
{
    \$this->$propertyName = $$lowerCamelName;

    return \$this;
}";
        $this->classFile->addMethod($method);
    }

    /**
     * @param $propertyName
     * @param $upperCamelName
     * @param $propertyType
     */
    public function addListSetter($propertyName, $upperCamelName, $propertyType)
    {
        $lowerCamelName = lcfirst($upperCamelName);
        $method = "public function set$upperCamelName(array $$lowerCamelName)
{
    \$this->$propertyName = $$lowerCamelName;

    return \$this;
}";
        $this->classFile->addMethod($method);
    }
}
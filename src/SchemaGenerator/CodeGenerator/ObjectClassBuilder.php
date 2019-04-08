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
    protected function addProperty($propertyName)
    {
        $this->classFile->addProperty($propertyName);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     */
    protected function addScalarSetter($propertyName, $upperCamelName)
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
     * @param string $propertyName
     * @param string $upperCamelName
     * @param string $propertyType
     */
    protected function addListSetter(string $propertyName, string $upperCamelName, string $propertyType)
    {
        $lowerCamelName = lcfirst($upperCamelName);
        $method = "public function set$upperCamelName(array $$lowerCamelName)
{
    \$this->$propertyName = $$lowerCamelName;

    return \$this;
}";
        $this->classFile->addMethod($method);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     * @param string $objectClass
     */
    protected function addObjectSetter(string $propertyName, string $upperCamelName, string $objectClass)
    {
        $lowerCamelName = lcfirst(str_replace('_', '', $objectClass));
        $method         = "public function set$upperCamelName($objectClass $$lowerCamelName)
{
    \$this->$propertyName = $$lowerCamelName;

    return \$this;
}";
        $this->classFile->addMethod($method);
    }
}
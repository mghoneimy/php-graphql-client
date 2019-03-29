<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;

/**
 * Class EnumObjectBuilder
 *
 * @package GraphQL\SchemaGenerator\CodeGenerator
 */
class EnumObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * EnumObjectBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     */
    public function __construct(string $writeDir, string $objectName)
    {
        $className = $objectName . 'EnumObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('EnumObject');
    }

    /**
     * @param string $valueName
     */
    public function addEnumValue(string $valueName)
    {
        $constantName = strtoupper($valueName);
        $this->classFile->addConstant($constantName, $valueName);
    }

    /**
     * @return void
     */
    public function build()
    {
        $this->classFile->writeFile();
    }
}
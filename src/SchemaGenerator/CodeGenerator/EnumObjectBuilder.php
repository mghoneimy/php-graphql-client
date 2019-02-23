<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;

class EnumObjectBuilder implements ObjectBuilderInterface
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * EnumObjectBuilder constructor.
     *
     * @param $writeDir
     * @param $objectName
     */
    public function __construct($writeDir, $objectName)
    {
        $className = $objectName . 'EnumObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('EnumObject');
    }

    /**
     * @param $valueName
     */
    public function addEnumValue($valueName)
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
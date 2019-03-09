<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;

/**
 * Class QueryObjectClassBuilder
 *
 * @package GraphQL\SchemaManager\CodeGenerator
 */
class QueryObjectClassBuilder extends ObjectClassBuilder
{
    /**
     * QueryObjectClassBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     */
    public function __construct(string $writeDir, string $objectName)
    {
        $className = $objectName . 'QueryObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('QueryObject');
        $this->classFile->addConstant('OBJECT_NAME', $objectName);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     */
    public function addSimpleSelector(string $propertyName, string $upperCamelName)
    {
        $method = "public function select$upperCamelName()
{
    \$this->selectField('$propertyName');

    return \$this;
}";
        $this->classFile->addMethod($method);
    }

    /**
     * @param string $fieldName
     * @param string $upperCamelName
     * @param string $fieldTypeName
     */
    public function addObjectSelector(string $fieldName, string $upperCamelName, string $fieldTypeName)
    {
        $objectClassName = $fieldTypeName . 'QueryObject';
        $method = "public function select$upperCamelName()
{
    \$object = new $objectClassName('$fieldName');
    \$this->selectField(\$object);

    return \$object;
}";
        $this->classFile->addMethod($method);
    }

    /**
     * This method builds the class and writes it to the file system
     */
    public function build(): void
    {
        $this->classFile->writeFile();
    }
}
<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;
use GraphQL\SchemaObject\QueryObject;

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

        // Special case for handling root query object
        if ($objectName === QueryObject::ROOT_QUERY_OBJECT_NAME) {
            $objectName = 'query';
        }
        $this->classFile->addConstant('OBJECT_NAME', $objectName);
    }

    /**
     * @param string $fieldName
     */
    public function addScalarField(string $fieldName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->addSimpleSelector($fieldName, $upperCamelCaseProp);
    }

    /**
     * @param string $fieldName
     * @param string $typeName
     */
    public function addObjectField(string $fieldName, string $typeName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($fieldName);
        $this->addObjectSelector($fieldName, $upperCamelCaseProp, $typeName);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     */
    protected function addSimpleSelector(string $propertyName, string $upperCamelName)
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
    protected function addObjectSelector(string $fieldName, string $upperCamelName, string $fieldTypeName)
    {
        $objectClassName = $fieldTypeName . 'QueryObject';
        $method = "public function select$upperCamelName(array \$args = [])
{
    \$object = new $objectClassName('$fieldName');
    \$object->appendArguments(\$args);
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
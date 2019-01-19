<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 1:46 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator;

use GraphQL\SchemaManager\CodeGenerator\CodeFile\ClassFile;

/**
 * Class QueryObjectClassBuilder
 *
 * @package GraphQL\SchemaManager\CodeGenerator
 */
class QueryObjectClassBuilder
{
    /**
     * @var ClassFile
     */
    protected $classFile;

    /**
     * QueryObjectClassBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     *
     * @throws \Exception
     */
    public function __construct($writeDir, $objectName)
    {
        $className = $objectName . 'QueryObject';
        $traitName = $objectName . 'Trait';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace('GraphQL\\SchemaObject');
        $this->classFile->extendsClass('QueryObject');
        $this->classFile->addTrait($traitName);
        $this->classFile->addConstant('OBJECT_NAME', $objectName);
    }

    /**
     * @param string $propertyName
     * @param string $upperCamelName
     */
    public function addSetter($propertyName, $upperCamelName)
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
     */
    public function addSimpleSelector($propertyName, $upperCamelName)
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
     * @param string $fieldClassType
     */
    public function addObjectSelector($fieldName, $upperCamelName, $fieldClassType)
    {
        $objectClassName = $fieldClassType . 'QueryObject';
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
    public function build()
    {
        $this->classFile->writeFile();
    }
}
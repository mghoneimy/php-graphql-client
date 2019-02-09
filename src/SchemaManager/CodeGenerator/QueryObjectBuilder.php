<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/9/19
 * Time: 8:46 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator;

class QueryObjectBuilder
{
    /**
     * @var QueryObjectClassBuilder
     */
    protected $classBuilder;

    /**
     * @var QueryObjectTraitBuilder
     */
    protected $traitBuilder;

    /**
     * SchemaObjectBuilder constructor.
     *
     * @param $writeDir
     * @param $objectName
     */
    public function __construct($writeDir, $objectName)
    {
        $this->classBuilder = new QueryObjectClassBuilder($writeDir, $objectName);
        $this->traitBuilder = new QueryObjectTraitBuilder($writeDir, $objectName);
    }

    public function addScalarProperty($propertyName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($propertyName);
        $this->traitBuilder->addProperty($propertyName);
        $this->classBuilder->addSetter($propertyName, $upperCamelCaseProp);
        $this->classBuilder->addSimpleSelector($propertyName, $upperCamelCaseProp);
    }

    /**
     * @param $propertyName
     * @param $typeName
     */
    public function addObjectProperty($propertyName, $typeName)
    {
        $upperCamelCaseProp = $this->getUpperCamelCase($propertyName);
        $this->classBuilder->addObjectSelector($propertyName, $upperCamelCaseProp, $typeName);
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

    /**
     *
     */
    public function build()
    {
        $this->classBuilder->build();
        $this->traitBuilder->build();
    }
}
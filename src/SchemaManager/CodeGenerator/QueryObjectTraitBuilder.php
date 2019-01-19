<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 1:49 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator;

use GraphQL\SchemaManager\CodeGenerator\CodeFile\TraitFile;

/**
 * Class QueryObjectTraitBuilder
 *
 * @package GraphQL\SchemaManager\CodeGenerator
 */
class QueryObjectTraitBuilder
{
    /**
     * @var TraitFile
     */
    protected $traitFile;

    /**
     * QueryObjectTraitBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     *
     * @throws \Exception
     */
    public function __construct($writeDir, $objectName)
    {
        $traitName = $objectName . 'Trait';

        $this->traitFile = new TraitFile($writeDir, $traitName);
        $this->traitFile->setNamespace('GraphQL\\SchemaObject');
    }

    /**
     * @param string $fieldName
     */
    public function addProperty($fieldName)
    {
        $this->traitFile->addProperty($fieldName);
    }

    /**
     * This method builds the class and writes it to the file system
     */
    public function build()
    {
        $this->traitFile->writeFile();
    }
}
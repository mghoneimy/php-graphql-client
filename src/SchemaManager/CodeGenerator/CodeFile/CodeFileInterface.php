<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/19/19
 * Time: 1:44 PM
 */

namespace GraphQL\SchemaManager\CodeGenerator\CodeFile;

/**
 * Interface that all classes which represent code files have to implement
 *
 * Interface CodeFileInterface
 *
 * @package GraphQL\SchemaManager\CodeGenerator\CodeFile
 */
interface CodeFileInterface
{
    /**
     * This method generates the file contents from the file format and contents
     */
    public function writeFile();
}
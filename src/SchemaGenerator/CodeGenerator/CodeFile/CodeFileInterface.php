<?php

namespace GraphQL\SchemaGenerator\CodeGenerator\CodeFile;

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
    public function writeFile(): bool;
}
<?php

namespace GraphQL\SchemaGenerator\CodeGenerator;

use GraphQL\SchemaGenerator\CodeGenerator\CodeFile\ClassFile;
use GraphQL\Util\StringLiteralFormatter;

/**
 * Class InputObjectClassBuilder
 *
 * @package GraphQL\SchemaGenerator\CodeGenerator
 */
class InputObjectClassBuilder extends ObjectClassBuilder
{
    /**
     * SchemaObjectBuilder constructor.
     *
     * @param string $writeDir
     * @param string $objectName
     * @param string $namespace
     */
    public function __construct(string $writeDir, string $objectName, string $namespace = self::DEFAULT_NAMESPACE)
    {
        $className = $objectName . 'InputObject';

        $this->classFile = new ClassFile($writeDir, $className);
        $this->classFile->setNamespace($namespace);
        if ($namespace !== self::DEFAULT_NAMESPACE) {
            $this->classFile->addImport('GraphQL\\SchemaObject\\InputObject');
        }
        $this->classFile->extendsClass('InputObject');
    }

    /**
     * @param string $argumentName
     */
    public function addScalarValue(string $argumentName)
    {
        $upperCamelCaseArg = StringLiteralFormatter::formatUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addScalarSetter($argumentName, $upperCamelCaseArg);
    }

    /**
     * @param string $argumentName
     * @param string $typeName
     */
    public function addListValue(string $argumentName, string $typeName)
    {
        $upperCamelCaseArg = StringLiteralFormatter::formatUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addListSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @param string $argumentName
     * @param string $typeName
     */
    public function addInputObjectValue(string $argumentName, string $typeName)
    {
        $typeName .= 'InputObject';
        $upperCamelCaseArg = StringLiteralFormatter::formatUpperCamelCase($argumentName);
        $this->addProperty($argumentName);
        $this->addObjectSetter($argumentName, $upperCamelCaseArg, $typeName);
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->classFile->writeFile();
    }
}
<?php

namespace GraphQL\SchemaGenerator;

use GraphQL\Client;
use GraphQL\SchemaGenerator\CodeGenerator\ArgumentsObjectClassBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\QueryObjectClassBuilder;
use GraphQL\SchemaObject\QueryObject;
use GraphQL\Util\StringLiteralFormatter;
use RuntimeException;

/**
 * This class scans the GraphQL API schema and generates Classes that map to the schema objects' structure
 *
 * Class SchemaClassGenerator
 *
 * @package GraphQL
 */
class SchemaClassGenerator
{
    /**
     * @var SchemaInspector
     */
    protected $schemaInspector;

    /**
     * @var string
     */
	private $writeDir;

    /**
     * This array is used as a set to store the already generated objects
     * Array structure: [$objectName] => true
     *
     * @var array
     */
	private $generatedObjects;

    /**
     * SchemaClassGenerator constructor.
     *
     * @param Client $client
     * @param string $writeDir
     */
	public function __construct(Client $client, string $writeDir = '')
    {
        $this->schemaInspector  = new SchemaInspector($client);
        $this->generatedObjects = [];
        $this->writeDir         = $writeDir;
        $this->setWriteDir();
    }

    /**
     *
     */
	public function generateRootQueryObject()
	{
	    $queryType = $this->schemaInspector->getQueryTypeSchema();
	    $rootObjectName = QueryObject::ROOT_QUERY_OBJECT_NAME;
	    $queryTypeName  = $queryType['name'];
	    //$rootObjectDescr = $queryType['description'];
	    $queryObjectBuilder = new QueryObjectClassBuilder($this->writeDir, $rootObjectName);
        $this->generatedObjects[$queryTypeName] = true;
        $this->appendObjectFields($queryObjectBuilder, $rootObjectName, $queryType['fields']);

        $queryObjectBuilder->build();
    }

    /**
     * This method receives the array of object fields as an input and adds the fields to the query object building
     *
     * @param QueryObjectClassBuilder $queryObjectBuilder
     * @param string                  $currentTypeName
     * @param array                   $fieldsArray
     */
	private function appendObjectFields(QueryObjectClassBuilder $queryObjectBuilder, string $currentTypeName, array $fieldsArray)
    {
        foreach ($fieldsArray as $fieldArray) {
            $name = $fieldArray['name'];
            // Skip fields with name "query"
            if ($name === 'query') continue;

            //$description = $fieldArray['description'];
            [$typeName, $typeKind] = $this->getTypeInfo($fieldArray);

            if ($typeKind === 'SCALAR') {
                $queryObjectBuilder->addScalarField($name);
            } else {

                // Generate nested type object if it wasn't generated
                $objectGenerated = array_key_exists($typeName, $this->generatedObjects) ? :
                    $this->generateObject($typeName, $typeKind);
                if ($objectGenerated) {

                    // Generate nested type arguments object if it wasn't generated
                    $argsObjectName = $currentTypeName . StringLiteralFormatter::formatUpperCamelCase($name);
                    $mapGenerated = array_key_exists($argsObjectName, $this->generatedObjects) ? :
                        $this->generateArgumentsMap($argsObjectName, $fieldArray['args']);
                    if ($mapGenerated) {

                        // Add sub type as a field to the query object if all generation happened successfully
                        $queryObjectBuilder->addObjectField($name, $typeName, $argsObjectName);
                    }
                }
            }
        }
    }

    /**
     * @param string $objectName
     * @param string $objectKind
     *
     * @return bool
     */
    private function generateObject(string $objectName, string $objectKind): bool
    {
        switch ($objectKind) {
            case 'OBJECT':
                return $this->generateQueryObject($objectName);
            case 'INPUT_OBJECT':
                return $this->generateInputObject($objectName);
            case 'ENUM':
                return $this->generateEnumObject($objectName);
            default:
                throw new RuntimeException('Unsupported object type');
        }
    }

    /**
     * @param string $objectName
     *
     * @return bool
     */
    private function generateQueryObject(string $objectName): bool
    {
        $objectArray = $this->schemaInspector->getObjectSchema($objectName);
        $objectBuilder = new QueryObjectClassBuilder($this->writeDir, $objectName);

        $this->generatedObjects[$objectName] = true;
        $this->appendObjectFields($objectBuilder, $objectName, $objectArray['fields']);
        $objectBuilder->build();

        return true;
    }

    /**
     * @param string $objectName
     *
     * @return bool
     */
    private function generateInputObject(string $objectName): bool
    {
        $objectArray = $this->schemaInspector->getInputObjectSchema($objectName);
        $objectBuilder = new InputObjectClassBuilder($this->writeDir, $objectName);

        $this->generatedObjects[$objectName] = true;
        foreach ($objectArray['inputFields'] as $inputFieldArray) {
            $name = $inputFieldArray['name'];
            //$description = $inputFieldArray['description'];
            //$defaultValue = $inputFieldArray['defaultValue'];
            [$typeName, $typeKind, $typeKindWrappers] = $this->getTypeInfo($inputFieldArray);

            if ($typeKind === 'SCALAR') {
                $objectBuilder->addScalarValue($name);
            } else {
                $objectGenerated = array_key_exists($typeName, $this->generatedObjects) ?: $this->generateObject($typeName, $typeKind);
                if ($objectGenerated) {
                    if (in_array('LIST', $typeKindWrappers)) {
                        $objectBuilder->addListValue($name, $typeName);
                    } else {
                        $objectBuilder->addInputObjectValue($name, $typeName);
                    }
                }
            }
        }

        $objectBuilder->build();

        return true;
    }

    /**
     * @param string $objectName
     *
     * @return bool
     */
    private function generateEnumObject(string $objectName): bool
    {
        $objectArray = $this->schemaInspector->getEnumObjectSchema($objectName);
        $objectBuilder = new EnumObjectBuilder($this->writeDir, $objectName);

        $this->generatedObjects[$objectName] = true;
        foreach ($objectArray['enumValues'] as $enumValue) {
            $name        = $enumValue['name'];
            //$description = $enumValue['description'];
            $objectBuilder->addEnumValue($name);
        }
        $objectBuilder->build();

        return true;
    }

    /**
     * @param string $argsMapName
     * @param array  $arguments
     *
     * @return bool
     */
    private function generateArgumentsMap(string $argsMapName, array $arguments): bool
    {
        $objectBuilder = new ArgumentsObjectClassBuilder($this->writeDir, $argsMapName);

        $this->generatedObjects[$argsMapName] = true;
        foreach ($arguments as $argumentArray) {
            $name = $argumentArray['name'];
            //$description = $inputFieldArray['description'];
            //$defaultValue = $inputFieldArray['defaultValue'];
            [$typeName, $typeKind, $typeKindWrappers] = $this->getTypeInfo($argumentArray);

            if ($typeKind === 'SCALAR') {
                $objectBuilder->addScalarArgument($name);
            } else {
                $objectGenerated = array_key_exists($typeName, $this->generatedObjects) ?: $this->generateObject($typeName, $typeKind);
                if ($objectGenerated) {
                    if (in_array('LIST', $typeKindWrappers)) {
                        $objectBuilder->addListArgument($name, $typeName);
                    } else {
                        $objectBuilder->addInputObjectArgument($name, $typeName);
                    }
                }
            }
        }
        $objectBuilder->build();

        return true;
    }

    /**
     * @param array $dataArray : The subarray which contains the key "type"
     *
     * @return array : Array formatted as [$typeName, $typeKind, $typeKindWrappers]
     */
    private function getTypeInfo(array $dataArray): array
    {
        $typeArray = $dataArray['type'];
        $typeWrappers = [];
        while ($typeArray['ofType'] !== null) {
            $typeWrappers[] = $typeArray['kind'];
            $typeArray = $typeArray['ofType'];
        }
        $typeInfo = [$typeArray['name'], $typeArray['kind'], $typeWrappers];

        return $typeInfo;
    }

    /**
     * Sets the write directory if it's not set for the class
     */
	private function setWriteDir(): void
    {
        if ($this->writeDir !== '') return;

        $currentDir = dirname(__FILE__);
        while (basename($currentDir) !== 'php-graphql-client') {
            $currentDir = dirname($currentDir);
        }

        $this->writeDir = $currentDir . '/schema_object';
    }

    /**
     * @return string
     */
    public function getWriteDir(): string
    {
        $this->setWriteDir();

        return $this->writeDir;
    }
}
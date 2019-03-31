<?php

namespace GraphQL\SchemaGenerator;

use GraphQL\Client;
use GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder;
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
	    $rootObjectName = 'Root';
	    $queryTypeName  = $queryType['name'];
	    //$rootObjectDescr = $queryType['description'];
	    $queryObjectBuilder = new QueryObjectBuilder($this->writeDir, $rootObjectName);
        $this->generatedObjects[$queryTypeName] = true;
        $this->appendObjectFields($queryObjectBuilder, $queryType['fields']);

        $queryObjectBuilder->build();
    }

    /**
     * This method receives the array of object fields as an input and adds the fields to the query object building
     *
     * @param QueryObjectBuilder $queryObjectBuilder
     * @param array              $fieldsArray
     */
	private function appendObjectFields(QueryObjectBuilder $queryObjectBuilder, array $fieldsArray)
    {
        foreach ($fieldsArray as $fieldArray) {
            $name = $fieldArray['name'];
            //$description = $fieldArray['description'];
            [$typeName, $typeKind] = $this->getTypeInfo($fieldArray);

            if ($typeKind === 'SCALAR') {
                $queryObjectBuilder->addScalarField($name);
            } else {
                $objectGenerated = array_key_exists($typeName, $this->generatedObjects) ?: $this->generateObject($typeName, $typeKind);
                if ($objectGenerated) {
                    $queryObjectBuilder->addObjectField($name, $typeName);
                }
            }

            //// Get query object args
            //$arguments = $fieldArray['args'];
            //foreach ($arguments as $argument) {
            //    $this->generateObjectArguments($queryObjectBuilder, $argument, $writeDir);
            //}

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
        $objectBuilder = new QueryObjectBuilder($this->writeDir, $objectName);

        $this->generatedObjects[$objectName] = true;
        $this->appendObjectFields($objectBuilder, $objectArray['fields']);
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
                $objectGenerated = false;
                if ($typeKind === 'OBJECT' || $typeKind === 'INPUT_OBJECT'){
                    $objectGenerated = array_key_exists($typeName, $this->generatedObjects) ?: $this->generateObject($typeName, $typeKind);
                }
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
     * @param QueryObjectBuilder $queryObjectBuilder
     * @param array              $argumentArray
     * @param string             $writeDir
     */
    private function generateObjectArguments(
        QueryObjectBuilder $queryObjectBuilder,
        array $argumentArray,
        string $writeDir = ''
    ): void
    {
        $argName = $argumentArray['name'];
        $argDescription = $argumentArray['description'];
        $argType = $argumentArray['type'];

        $argKind = $argType['kind'];
        if ($argKind === 'SCALAR') {
            $argTypeName = $argType['name'];
            $argTypeDescription = $argType['description'];
            $queryObjectBuilder->addScalarArgument($argName);
        } elseif ($argKind === 'INPUT_OBJECT') {
            $argTypeName = $argType['name'];
            $argTypeDescription = $argType['description'];
            $queryObjectBuilder->addInputObjectArgument($argName, $argTypeName);

            // Generate input object class
            $this->generateInputObject($argTypeName, $argType['inputFields'], $writeDir);
        } elseif ($argKind === 'LIST') {
            // Get type wrapped by list
            $wrappedType = $argType['ofType'];
            $wrappedTypeName = $wrappedType['name'];
            $wrappedTypeDescription = $wrappedType['description'];
            $wrappedTypeKind = $wrappedType['kind'];
            $queryObjectBuilder->addListArgument($argName, $wrappedTypeName);

            // Handle generation of ENUM object if needed
            if ($wrappedTypeKind === 'ENUM') {
                $this->generateEnumObject($wrappedTypeName, $wrappedType['enumValues'], $writeDir);
            }
        }
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
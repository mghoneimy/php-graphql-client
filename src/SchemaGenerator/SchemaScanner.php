<?php

namespace GraphQL\SchemaGenerator;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\SchemaGenerator\CodeGenerator\EnumObjectBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\InputObjectClassBuilder;
use GraphQL\SchemaGenerator\CodeGenerator\QueryObjectBuilder;

/**
 * This class scans the GraphQL API schema and generates Classes that map to the schema objects' structure
 *
 * Class SchemaScanner
 *
 * @package GraphQL
 */
class SchemaScanner
{
    /**
     * @var string
     */
	const SCHEMA_QUERY = "
	{
      __schema {
        queryType {
          
          
          ## Get query type name, type, and description
          name
          kind
          description
          
          
          ## Get all query type fields, which are other object type wrappers
          fields {
    
    
            ## For each wrapper, get its name and description
            name
            description
            ## For each wrapper, get its type name and kind (which is a LIST)
            type {
              name
              kind
              ## For each list, get its type declaration
              ofType {
                name
                kind
                description
                ## For each type, get its fields with their types
                fields {
                  name
                  description
                  type {
                    name
                    description
                    kind
                    ofType {
                      name
                      description
                      kind
                    }
                  }
                }
              }
            }
            ## Get arguments of each type
            args {
              name
              description
              type {
                name
                description
                kind
                ## Filter types are not wrapped, so we need to get the input fields in the external type
                inputFields {
                  name
                  description
                  type {
                    name
                    description
                    kind
                    ofType{
                      name
                      description
                      kind
                      ofType {
                        name
                        description
                        kind
                      }
                    }
                  }
                }
                ofType {
                  name
                  description
                  kind
                  ## Ordering types are wrapped, so we need to get enum values inside the wrapped type
                  enumValues {
                    name
                    description
                  }
                }
              }
            }
    
    
          }
        }
      }
    }";

    /**
     * @var string
     */
	private $writeDir;

    /**
     * SchemaScanner constructor.
     */
	public function __construct()
    {
        $this->writeDir = '';
    }

    /**
     * @param string $endpointUrl
     * @param array  $authorizationHeaders
     *
     * @return array
     * @throws QueryError
     */
	public function getSchemaTypesArray(string $endpointUrl, array $authorizationHeaders = []): array
    {
        // Read schema form GraphQL endpoint
        $response = (new Client($endpointUrl, $authorizationHeaders))->runRawQuery(
            self::SCHEMA_QUERY, true
        );
        $schemaTypes = $response->getData()['__schema']['queryType']['fields'];

        return $schemaTypes;
    }

    /**
     * @param array  $schemaTypes
     * @param string $writeDir
     */
	public function generateSchemaObjects(array $schemaTypes, string $writeDir = '')
	{
	    if (empty($writeDir)) $writeDir = $this->getWriteDir();

        foreach ($schemaTypes as $typeObject) {
            $name = $typeObject['name'];
            $description = $typeObject['description'];
            $type = $typeObject['type'];
            $arguments = $typeObject['args'];
            $queryObjectBuilder = new QueryObjectBuilder($writeDir, $name);

            if ($type['name'] === null && $type['kind'] !== 'OBJECT') {
                $type = $type['ofType'];
                $description = $type['description'];
            }
            // Build query object fields
            foreach ($type['fields'] as $field) {
                $this->generateObjectFields($queryObjectBuilder, $field);
            }

            // Get query object args
            foreach ($arguments as $argument) {
                $this->generateObjectArguments($queryObjectBuilder, $argument, $writeDir);
            }

		    $queryObjectBuilder->build();
        }
	}

    /**
     * @param QueryObjectBuilder $queryObjectBuilder
     * @param array              $fieldArray
     */
	private function generateObjectFields(QueryObjectBuilder $queryObjectBuilder, array $fieldArray)
    {
        $propertyName = $fieldArray['name'];
        $fieldDescription = $fieldArray['description'];

        $isScalar = $fieldArray['type']['kind'] === 'SCALAR';
        if ($isScalar) {
            $typeName = $fieldArray['type']['name'];
            $queryObjectBuilder->addScalarField($propertyName);
        } else {
            $typeName = $fieldArray['type']['ofType']['name'];
            $queryObjectBuilder->addObjectField($propertyName, $typeName);
        }
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
    )
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
     * @param string $objectName
     * @param array  $fieldsList
     * @param string $writeDir
     */
    private function generateInputObject($objectName, array $fieldsList, string $writeDir = '')
    {
        if (empty($writeDir)) $writeDir = $this->getWriteDir();

        $inputObjectBuilder = new InputObjectClassBuilder($writeDir, $objectName);
        foreach ($fieldsList as $field) {
            $fieldName = $field['name'];
            $fieldDescription = $field['description'];
            $fieldType = $field['type'];
            switch ($fieldType['kind']) {
                case 'SCALAR':
                    $inputObjectBuilder->addScalarValue($fieldName);
                    break;
                case 'INPUT_OBJECT':
                    $inputObjectBuilder->addInputObjectValue($fieldName, $fieldType['name']);
                    break;
                case 'LIST':
                    // Get wrapped object type
                    while (!in_array($fieldType['kind'], ['OBJECT', 'INPUT_OBJECT', 'SCALAR']))
                        $fieldType = $fieldType['ofType'];
                    $wrappedTypeName = $fieldType['name'];
                    $wrappedTypeDescription = $fieldType['description'];
                    $inputObjectBuilder->addListValue($fieldName, $wrappedTypeName);
                    break;
            }
        }
        $inputObjectBuilder->build();
    }

    /**
     * @param string $objectName
     * @param array  $values
     * @param string $writeDir
     */
    private function generateEnumObject($objectName, array $values, string $writeDir = '')
    {
        if (empty($writeDir)) $writeDir = $this->getWriteDir();

        $enumBuilder = new EnumObjectBuilder($writeDir, $objectName);
        foreach ($values as $value) {
            $valueName = $value['name'];
            $valueDescripion = $value['description'];
            $enumBuilder->addEnumValue($valueName);
        }
        $enumBuilder->build();
    }

    /**
     * Sets the write directory if it's not set for the class
     */
	private function setWriteDir()
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
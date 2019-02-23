<?php

namespace GraphQL\SchemaGenerator;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
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
	private $writeDir = '';

    /**
     * @param string $endpointUrl
     * @param array  $authorizationHeaders
     *
     * @return array
     * @throws QueryError
     */
	public function getSchemaTypesArray($endpointUrl, $authorizationHeaders = [])
    {
        // Read schema form GraphQL endpoint
        $response = (new Client($endpointUrl, $authorizationHeaders))->runRawQuery(self::SCHEMA_QUERY, true);
        $schemaTypes   = $response->getData()['__schema']['queryType']['fields'];

        return $schemaTypes;
    }

    public function buildTypesTree(array $schemaTypes)
    {

    }

    /**
     * @param array  $schemaTypes
     * @param string $writeDir
     */
	public function generateSchemaObjects(array $schemaTypes, $writeDir = '')
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
                $propertyName = $field['name'];
                $fieldDescription = $field['description'];

                $isScalar = $field['type']['kind'] === 'SCALAR';
                if ($isScalar) {
                    $typeName = $field['type']['name'];
                    $queryObjectBuilder->addScalarField($propertyName);
                } else {
                    $typeName = $field['type']['ofType']['name'];
                    $queryObjectBuilder->addObjectField($propertyName, $typeName);
                }
            }

            // Get query object args
            foreach ($arguments as $argument) {
                $argName = $argument['name'];
                $argDescription = $argument['description'];
                $argType = $argument['type'];

                $argKind = $argType['kind'];
                if ($argKind === 'SCALAR') {
                    $argTypeName = $argType['name'];
                    $argTypeDescription = $argType['description'];
                    $queryObjectBuilder->addScalarArgument($argName);
                } elseif ($argKind === 'INPUT_OBJECT') {
                    $argTypeName = $argType['name'];
                    $argTypeDescription = $argType['description'];
                    //$queryObjectBuilder->addInputObjectArgument($argName, $argTypeName);
                    // TODO: Handle input fields
                } elseif ($argKind === 'LIST') {
                    // Assume list of objects for now
                    $argType = $argType['ofType'];
                    $argTypeName = $argType['name'];
                    $argTypeDescription = $argType['description'];
                    $queryObjectBuilder->addListArgument($argName, $argTypeName);
                    // TODO: Handle Enum types
                }
            }

		    $queryObjectBuilder->build();
        }
	}

    /**
     * Sets the write directory if it's not set for the class
     */
	private function setWriteDir()
    {
        if ($this->writeDir !== '') return;

        $currentDir = dirname(__FILE__);
        while (basename($currentDir) !== 'graphql-client') {
            $currentDir = dirname($currentDir);
        }

        $this->writeDir = $currentDir . '/schema_object';
    }

    /**
     * @return string
     */
    public function getWriteDir()
    {
        $this->setWriteDir();

        return $this->writeDir;
    }
}
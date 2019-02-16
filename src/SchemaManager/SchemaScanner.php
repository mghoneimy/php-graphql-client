<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/18/19
 * Time: 12:42 AM
 */

namespace GraphQL\SchemaManager;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\SchemaManager\CodeGenerator\QueryObjectBuilder;

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
	  __schema{
		types{
		  name
		  kind
		  description
		  fields {
			name
			description
			type {
			  name
			  kind
			  ofType {
				name
				kind
			  }
			}
		  }
		}
	  }
	}";

    /**
     * @var string
     */
	private static $writeDir = '';

    /**
     * @param string $endpointUrl
     * @param array  $authorizationHeaders
     *
     * @return array
     * @throws QueryError
     */
	public static function getSchemaArrayTypes($endpointUrl, $authorizationHeaders = [])
    {
        // Read schema form GraphQL endpoint
        $response = (new Client($endpointUrl, $authorizationHeaders))->runRawQuery(self::SCHEMA_QUERY, true);
        $schemaTypes   = $response->getData()['__schema']['types'];

        // Filter out object types only
        $schemaTypes = array_filter($schemaTypes, function($element) {
            return ($element['kind'] == 'OBJECT' && $element['name'] !== 'QueryType' && $element['name'][0] !== '_');
        });

        return $schemaTypes;
    }

    /**
     * @param array  $schemaTypes
     * @param string $writeDir
     */
	public static function generateSchemaObjects(array $schemaTypes, $writeDir = '')
	{
	    if (empty($writeDir)) $writeDir = static::getWriteDir();

        foreach ($schemaTypes as $typeObject) {
            $name        = $typeObject['name'];
            $description = $typeObject['description'];
            $schemaObjectBuilder = new QueryObjectBuilder($writeDir, $name);

            // Get type fields details
		    foreach ($typeObject['fields'] as $property) {
		        $propertyName = $property['name'];
		        //$fieldDescription = $property['description'];

		        $isScalar = $property['type']['kind'] === 'SCALAR';
                if ($isScalar) {
                    //$typeName = $property['type']['name'];
                    $schemaObjectBuilder->addScalarProperty($propertyName);
                } else {
                    $typeName = $property['type']['ofType']['name'];
                    $schemaObjectBuilder->addObjectProperty($propertyName, $typeName);
                }
            }
		    $schemaObjectBuilder->build();
        }
	}

    /**
     * Sets the write directory if it's not set for the class
     */
	private static function setWriteDir()
    {
        if (static::$writeDir !== '') return;

        $currentDir = dirname(__FILE__);
        while (basename($currentDir) !== 'graphql-client') {
            $currentDir = dirname($currentDir);
        }

        static::$writeDir = $currentDir . '/schema_object';
    }

    /**
     * @return string
     */
    public static function getWriteDir()
    {
        static::setWriteDir();

        return static::$writeDir;
    }
}
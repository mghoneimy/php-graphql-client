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
use GraphQL\SchemaManager\CodeGenerator\QueryObjectClassBuilder;
use GraphQL\SchemaManager\CodeGenerator\QueryObjectTraitBuilder;

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
     * @param $endpointUrl
     * @param $authorizationHeaders
     *
     * @return array
     * @throws QueryError
     */
	public static function getSchemaArrayType($endpointUrl, $authorizationHeaders)
    {
        // Read schema form GraphQL endpoint
        $response = (new Client($endpointUrl, $authorizationHeaders))->runRawQuery(self::SCHEMA_QUERY, true);
        $schema   = $response->getData()['__schema']['types'];

        // Filter out object types only
        $schema = array_filter($schema, function($element) {
            return ($element['kind'] == 'OBJECT' && $element['name'] !== 'QueryType' && $element['name'][0] !== '_');
        });

        return $schema;
    }

    /**
     * @param array  $schemaTypes
     * @param string $writeDir
     */
	public static function readSchema(array $schemaTypes, $writeDir = '')
	{
	    if (empty($writeDir)) $writeDir = static::getWriteDir();

        foreach ($schemaTypes as $typeObject) {
            $name        = $typeObject['name'];
            $description = $typeObject['description'];

            $traitBuilder = new QueryObjectTraitBuilder($writeDir, $name);
            $classBuilder = new QueryObjectClassBuilder($writeDir, $name);

            // Get type fields details
		    foreach ($typeObject['fields'] as $field) {
		        $fieldName = $field['name'];

		        // Construct upper camel case name for field names
		        if (strpos($fieldName, '_') === false) {
                    $fieldCamelCName = ucfirst($fieldName);
                } else {
                    $fieldCamelCName  = str_replace('_', '', ucwords($fieldName, '_'));
                }
		        $fieldDescription = $field['description'];

		        $isScalar         = $field['type']['kind'] === 'SCALAR';
                if ($isScalar) {
                    $typeName = $field['type']['name'];
                    $traitBuilder->addProperty($fieldName);
                    $classBuilder->addSetter($fieldName, $fieldCamelCName);
                    $classBuilder->addSimpleSelector($fieldName, $fieldCamelCName);
                } else {
                    $typeName = $field['type']['ofType']['name'];
                    $classBuilder->addObjectSelector($fieldName, $fieldCamelCName, $typeName);
                }
            }
		    $traitBuilder->build();
		    $classBuilder->build();
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
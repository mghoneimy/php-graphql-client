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
	 * @param $endpointUrl
	 * @param $authorizationHeaders
	 *
	 * @throws QueryError
	 */
	public static function readSchema($endpointUrl, $authorizationHeaders)
	{
	    // Read schema form GraphQL endpoint
		$response = (new Client($endpointUrl, $authorizationHeaders))->runRawQuery(self::SCHEMA_QUERY, true);
        $schema   = $response->getData()['__schema']['types'];

        // Filter out object types only
        $schema = array_filter($schema, function($element) {
            return ($element['kind'] == 'OBJECT' && $element['name'] !== 'QueryType');
        });

        // Loop over schema to extract type definitions
        foreach ($schema as $typeObject) {

            // TODO: Write method to generate trait from type scalar attributes

            // TODO: Write method to generate class that extends QueryObject, uses ObjectTrait, and implement extra method

            $name        = $typeObject['name'];
            $description = $typeObject['description'];

            // Get type fields details
		    foreach ($typeObject['fields'] as $field) {
		        $fieldName        = $field['name'];
		        if (strpos($fieldName, '_') !== false) {
                    $fieldCamelCName = $fieldName;
                } else {
                    $fieldCamelCName  = lcfirst(str_replace('_', '', ucwords($fieldName, '_')));
                }
		        $fieldDescription = $field['description'];

		        $isScalar         = $field['type']['kind'] === 'SCALAR';
                if ($isScalar) {
                    $typeName = $field['type']['name'];
                } else {
                    $typeName = $field['type']['ofType']['name'];
                }
            }
        }
	}
}
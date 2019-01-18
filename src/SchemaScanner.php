<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 1/18/19
 * Time: 12:42 AM
 */

namespace GraphQL;

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
	 * @throws Exception\QueryError
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
            $name        = $typeObject['name'];
            $description = $typeObject['description'];
            $fields      = [];

            // Get type fields details
		    foreach ($typeObject['fields'] as $field) {
		        $tempField['name'] = $field['name'];
                $tempField['description'] = $field['description'];
                $tempField['is_scalar']   = $field['type']['kind'] === 'SCALAR';
                if ($tempField['is_scalar']) {
                    $tempField['type'] = $field['type']['name'];
                } else {
                    $tempField['type'] = $field['type']['ofType']['name'];
                }
                $fields[] = $tempField;
            }
        }
	}
}
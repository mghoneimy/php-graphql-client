<?php

namespace GraphQL\SchemaGenerator;

use GraphQL\Client;

/**
 * Class SchemaInspector
 *
 * @codeCoverageIgnore
 *
 * @package GraphQL\SchemaGenerator
 */
class SchemaInspector
{
    private const TYPE_SUB_QUERY = <<<QUERY
type{
  name
  kind
  description
  ofType{
    name
    kind
    ofType{
      name
      kind
      ofType{
        name
        kind
      }
    }
  }
}
QUERY;


    /**
     * @var Client
     */
    protected $client;

    /**
     * SchemaInspector constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getQueryTypeSchema(): array
    {
        $schemaQuery = "{
  __schema{
    queryType{
      name
      kind
      description
      fields{
        name
        description
        " . static::TYPE_SUB_QUERY . "
        args{
          name
          description
          defaultValue
          " . static::TYPE_SUB_QUERY . "
        }
      }
    }
  }
}";
        $response = $this->client->runRawQuery($schemaQuery, true);

        return $response->getData()['__schema']['queryType'];
    }

    /**
     * @param string $objectName
     *
     * @return array
     */
    public function getObjectSchema(string $objectName): array
    {
        $schemaQuery = "{
  __type(name: \"$objectName\") {
    name
    kind
    fields{
      name
      description
      " . static::TYPE_SUB_QUERY . "
      args{
        name
        description
        defaultValue
        " . static::TYPE_SUB_QUERY . "
      }
    }
  }
}";
        $response = $this->client->runRawQuery($schemaQuery, true);

        return $response->getData()['__type'];
    }

    /**
     * @param string $objectName
     *
     * @return array
     */
    public function getInputObjectSchema(string $objectName): array
    {
        $schemaQuery = "{
  __type(name: \"$objectName\") {
    name
    kind
    inputFields {
      name
      description
      defaultValue
      " . static::TYPE_SUB_QUERY . "
    }
  }
}";
        $response = $this->client->runRawQuery($schemaQuery, true);

        return $response->getData()['__type'];
    }

    /**
     * @param string $objectName
     *
     * @return array
     */
    public function getEnumObjectSchema(string $objectName): array
    {
        $schemaQuery = "{
  __type(name: \"$objectName\") {
    name
    kind
    enumValues {
      name
      description
    }
  }
}";
        $response = $this->client->runRawQuery($schemaQuery, true);

        return $response->getData()['__type'];
    }
}
# php-graphql-client
[![Build Status](https://travis-ci.org/mghoneimy/php-graphql-client.svg?branch=master)](https://travis-ci.org/mghoneimy/php-graphql-client)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/cb5e0708c4244c1a848be668dbcda8b0)](https://app.codacy.com/app/mghoneimy/php-graphql-client?utm_source=github.com&utm_medium=referral&utm_content=mghoneimy/php-graphql-client&utm_campaign=Badge_Grade_Dashboard)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/c2b0ae3a556547c38e1247d63228adde)](https://www.codacy.com/app/mghoneimy/php-graphql-client?utm_source=github.com&utm_medium=referral&utm_content=mghoneimy/php-graphql-client&utm_campaign=Badge_Coverage)

A GraphQL client written in PHP that provides very simple, yet powerful, query generator classes which make the process
of writing GraphQL queries a very simple one. The package also generates schema objects that can be used to write queries
based on the types declared in the API schema using the introspection feature in GraphQL, this feature is still under
development though.

# Installation
Run the following command to install the package using composer:
```
composer require gmostafa/php-graphql-client
```

# Query Example: Simple Query
```
$gql = (new Query('Company'))
    ->setSelectionSet(
        [
            'name',
            'serialNumber'
        ]
    );
```
This simple query will retrieve all companies displaying their names and serial numbers.

# Query Example: Nested Queries
```
$gql = (new Query('Company'))
    ->setSelectionSet(
        [
            'name',
            'serialNumber',
            (new Query('branches'))
                ->setSelectionSet(
                    [
                        'address',
                        (new Query('contracts'))
                            ->setSelectionSet(['date'])
                    ]
                )
        ]
    );
```
This query is a more complex one, retrieving not just scalar fields, but object fields as well. This query returns all
companies, displaying their names, serial numbers, and for each company, all its branches, displaying the branch address,
and for each address, it retrieves all contracts bound to this address, displaying their dates.

# Query Example: Query With Arguments
```
$gql = (new Query('Company'))
    ->setArguments(['name' => 'Tech Co.', 'first' => 3])
    ->setSelectionSet(
        [
            'name',
            'serialNumber'
        ]
    );
```
This query does not retrieve all companies by adding arguments. This query will retrieve the first 3 companies with the
name "Tech Co.", displaying their names and serial numbers. 

# Query Example: Query With Array Argument
```
$gql = (new Query('Company'))
    ->setArguments(['serialNumbers' => [159, 260, 371]])
    ->setSelectionSet(
        [
            'name',
            'serialNumber'
        ]
    );
```
This query is a special case of the arguments query. In this example, the query will retrieve only the companies with
serial number in one of 159, 260, and 371, displaying the name and serial number.

# Query Example: Query With Input Object Argument
```
$gql = (new Query('Company'))
    ->setArguments(['filter' => new RawObject('{name_starts_with: "Face"}')])
    ->setSelectionSet(
        [
            'name',
            'serialNumber'
        ]
    );
```
This query is another special case of the arguments query. In this example, we're setting a custom input object "filter"
with some values to limit the companies being returned. We're setting the filter "name_starts_with" with value "Face".
This query will retrieve only the companies whose names start with the phrase "Face".

The RawObject class being constructed is used for injecting the string into the query as it is. Whatever string is input
into the RawObject constructor will be put in the query as it is without any custom formatting normally done by the
query class.

# The Query Builder
The QueryBuilder class can be used to construct Query objects dynamically, which can be useful in some cases. It works
very similarly to the Query class, but the Query building is divided into steps.

That's how the "Query With Input Object Argument" example can be created using the
QueryBuilder:
```
$builder = (new QueryBuilder('Company'))
    ->setArgument('filter', new RawObject('{name_starts_with: "Face"}'))
    ->selectField('name')
    ->selectField('serialNumber');
$gql = $builder->getQuery();
```

# Constructing The Client
A Client object can easily be instantiated by providing the GraphQL endpoint URL. The Client constructor also receives
an optional "authorizationHeaders" array, which can be used to add authorization headers to all requests being sent to
the GraphQL server.

Example:
```
$client = new Client(
    'http://api.graphql.com',
    ['Authorization' => 'Basic xyz']
);
```

# Running Queries

Running query with the GraphQL client and getting the results in object structure:
```
$results = $client->runQuery($gql);
$results->getData()->Company[0]->branches;
```
Or getting results in array structure:
```
$results = $client->runQuery($gql, true);
$results->getData()['Company'][1]['branches']['address']
```

# Live API Example
GraphQL Pokemon is a very cool public GraphQL API available to retrieve Pokemon data. The API is available publicly on
the web, we'll use it to demo the capabilities of this client.

Github Repo link: https://github.com/lucasbento/graphql-pokemon
 
API link: https://graphql-pokemon.now.sh/

This query retrieves Pikachu's evolutions and their attacks:
```
{
  pokemon(name: "Pikachu") {
    id
    number
    name
    evolutions {
      id
      number
      name
      weight {
        minimum
        maximum
      }
      attacks {
        fast {
          name
          type
          damage
        }
      }
    }
  }
}

```

That's how this query can be written using the query class and run using the client:
```
$client = new Client(
    'https://graphql-pokemon.now.sh/'
);
$gql = (new Query('pokemon'))
    ->setArguments(['name' => 'Pikachu'])
    ->setSelectionSet(
        [
            'id',
            'number',
            'name',
            (new Query('evolutions'))
                ->setSelectionSet(
                    [
                        'id',
                        'number',
                        'name',
                        (new Query('attacks'))
                            ->setSelectionSet(
                                [
                                    (new Query('fast'))
                                        ->setSelectionSet(
                                            [
                                                'name',
                                                'type',
                                                'damage',
                                            ]
                                        )
                                ]
                            )
                    ]
                )
        ]
    );
try {
    $results = $client->runQuery($gql, true);
}
catch (QueryError $exception) {
    print_r($exception->getErrorDetails());
    exit;
}
print_r($results->getData()['pokemon']);
```
Or alternatively, That's how this query can be generated using the QueryBuilder class:
```
$client = new Client(
    'https://graphql-pokemon.now.sh/'
);
$builder = (new QueryBuilder('pokemon'))
    ->setArgument('name', 'Pikachu')
    ->selectField('id')
    ->selectField('number')
    ->selectField('name')
    ->selectField(
        (new QueryBuilder('evolutions'))
            ->selectField('id')
            ->selectField('name')
            ->selectField('number')
            ->selectField(
                (new QueryBuilder('attacks'))
                    ->selectField(
                        (new QueryBuilder('fast'))
                            ->selectField('name')
                            ->selectField('type')
                            ->selectField('damage')
                    )
            )
    );
$gql = $builder->getQuery();
try {
    $results = $client->runQuery($gql, true);
}
catch (QueryError $exception) {
    print_r($exception->getErrorDetails());
    exit;
}
print_r($results->getData()['pokemon']);
```
# Working With Schema Objects (Beta)
The greatest advantage of getting to use this package is that it generates GraphQL schema objects for you to interact
with, without having to write queries or worry about typos or about GraphQL syntax.

For instance, creating a GraphQL query can be something as simple as this:
```
$object = new TestQueryObject();
$object->setFilterBy(
    	(new _TestFilterInputObject())
    	    ->setIds([1, 2, 3])
    )
    ->setPropertyOne('val')
    ->selectPropertyOne()
    ->selectPropertyTwo()
    ->selectOtherObjects()
    	->selectName();
;
```
And then, this query objected can be converted to an actual query and run by the client:
```
$results = $client->runQueryObject($object);
``` 

# Generating The Schema Objects
Schema objects can easily be generated by executing the command:
```
php vendor/bin/generate_schema_objects
```
This script will retrieve the API schema types using the introspection feature in GraphQL, then generate the schema
objects from the types, and save them in the `schema_object` directory in the root directory of the package.
 
# The Schema Object Classes
The SchemaScanner will scan the API queryType recursively, creating a class for every object in the schema spec.
The SchemaScanner will generate a different schema class depending on the type of object being scanned using the
following mapping from GraphQL types to SchemaObject types:
- OBJECT: QueryObject
- INPUT_OBJECT: InputObject
- ENUM: EnumObject

# The QueryObject
The object generator will scan each query object declaration in the schema spec, building a corresponding class
according to the following rules:
- For a query object of name {object_name}, a class with name {object_name}QueryObject will be created
- For each argument in the query object arguments, a corresponding setter will be created to set the argument value
according to the following rules:
  - Scalar arguments: will have a simple setter created for them to set the scalar argument value.
  - List arguments: will have a list setter created for them to set the argument value with an array
  - Input object arguments: will have an input object setter created for them to set the argument value with an object
  of type `InputObject`
- For each selection field in the selection set of the query object, a corresponding selector method will be created,
according to the following rules:
  - Scalar fields will have a simple selector created for them, which will add the field name to the selection set.
  The simple selector will return a reference to the query object being created (this).
  - Object fields will have an object selector created for them, which will create a new query object internally and
  nest it inside the current query. The object selector will return instance of the new query object created.

Sample Query Object:
```
<?php

namespace GraphQL\SchemaObject;

class ComplexObjectQueryObject extends QueryObject
{
    const OBJECT_NAME = 'ComplexObject';

    protected $_id;
    protected $creation_date;
    protected $orderBy;
    protected $filterBy;

    public function selectId()
    {
        $this->selectField('_id');
    
        return $this;
    }

    public function selectSimples()
    {
        $object = new SimpleObjectQueryObject('simples');
        $this->selectField($object);
    
        return $object;
    }

    public function selectCreationDate()
    {
        $this->selectField('creation_date');
    
        return $this;
    }

    public function setId($id)
    {
        $this->_id = $id;
    
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }
    
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }
        
     public function setFilterBy(_FilterByInputObject $filterByInputObject)
     {
         $this->filterBy = $filterByInputObject;
         
         return $this;
     }   
}
```

# The InputObject
The object generator will scan each input object declaration in the schema spec, building a corresponding class
according to the following rules:
- For an input object of name {object_name}, a class with name {object_name}InputObject will be created
- For each scalar input field, a scalar setter will be created to set the input field value with a scalar value
- For each list input field, a list setter will be created to set the input field value with an array

Sample InputObject:
```
<?php

namespace GraphQL\SchemaObject;

class _FilterByInputObject extends InputObject
{
    protected $name;
    protected $name_contains;
    protected $name_not;
    protected $name_in;
    protected $name_not_in;

    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    public function setNameContains($nameContains)
    {
        $this->name_contains = $nameContains;
    
        return $this;
    }

    public function setNameNot($nameNot)
    {
        $this->name_not = $nameNot;
    
        return $this;
    }

    public function setNameIn(array $nameIn)
    {
        $this->name_in = $nameIn;
    
        return $this;
    }

    public function setNameNotIn(array $nameNotIn)
    {
        $this->name_not_in = $nameNotIn;
    
        return $this;
    }
}
```

# The EnumObject
The object generator will scan each enum object declaration in the schema spec, building a corresponding class
according to the following rules:
- For an enum object of name {object_name}, a class with name {object_name}EnumObject will be created
- For each EnumValue in the ENUM declaration, a const will be created to hold its value in the class 

Sample EnumObject:
```
<?php

namespace GraphQL\SchemaObject;

class _OrderingEnumObject extends EnumObject
{
    const CREATION_DATE_ASC = 'creation_date_asc';
    const CREATION_DATE_DESC = 'creation_date_desc';
}
```

# Supported Features
- Query builder
- GraphQL client to execute queries
- Results parsing in multiple formats
- Reporting API errors
- Generating schema objects from API types (Beta)
- Using schema objects to build queries
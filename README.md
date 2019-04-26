# PHP GraphQL Client
[![Build Status](https://travis-ci.org/mghoneimy/php-graphql-client.svg?branch=master)](https://travis-ci.org/mghoneimy/php-graphql-client)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/cb5e0708c4244c1a848be668dbcda8b0)](https://app.codacy.com/app/mghoneimy/php-graphql-client?utm_source=github.com&utm_medium=referral&utm_content=mghoneimy/php-graphql-client&utm_campaign=Badge_Grade_Dashboard)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/c2b0ae3a556547c38e1247d63228adde)](https://www.codacy.com/app/mghoneimy/php-graphql-client?utm_source=github.com&utm_medium=referral&utm_content=mghoneimy/php-graphql-client&utm_campaign=Badge_Coverage)
[![Total Downloads](https://poser.pugx.org/gmostafa/php-graphql-client/downloads)](https://packagist.org/packages/gmostafa/php-graphql-client)
[![Latest Stable Version](https://poser.pugx.org/gmostafa/php-graphql-client/v/stable)](https://packagist.org/packages/gmostafa/php-graphql-client)
[![License](https://poser.pugx.org/gmostafa/php-graphql-client/license)](https://packagist.org/packages/gmostafa/php-graphql-client)

A GraphQL client written in PHP which provides very simple, yet powerful, query generator classes that make the process
of interacting with a GraphQL server a very simple one. 

# Usage
There are 3 primary ways to use this package to generate your GraphQL queries:
1. Query Class: Simple class that maps to GraphQL queries. It's designed to manipulate queries with ease and speed.
2. QueryBuilder Class: Builder class that can be used to generate `Query` objects dynamically. It's design to be used in
cases where a query is being build in a dynamic fashion.  
3. PHP GraphQL-OQM: An extension to this package. It Eliminates the need to write any GraphQL queries or refer to the
API documentation or syntax. It generates query objects from the API schema, declaration exposed through GraphQL's
introspection, which can then be simply interacted with.

# Installation
Run the following command to install the package using composer:
```
composer require gmostafa/php-graphql-client
```

# Object-to-Query-Mapper Extension
To avoid the hassle of having to write _any_ queries and just interact with PHP objects generated from your API schema
visit PHP GraphQL OQM repository at:
https://github.com/mghoneimy/php-graphql-oqm

# Query Examples
## Simple Query
```
$gql = (new Query('companies'))
    ->setSelectionSet(
        [
            'name',
            'serialNumber'
        ]
    );
```
This simple query will retrieve all companies displaying their names and serial numbers.

## Nested Queries
```
$gql = (new Query('companies'))
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

## Query With Arguments
```
$gql = (new Query('companies'))
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

## Query With Array Argument
```
$gql = (new Query('companies'))
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

## Query With Input Object Argument
```
$gql = (new Query('companies'))
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
$builder = (new QueryBuilder('companies'))
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
try {
    $results = $client->runQuery($builder, true);
}
catch (QueryError $exception) {
    print_r($exception->getErrorDetails());
    exit;
}
print_r($results->getData()['pokemon']);
```

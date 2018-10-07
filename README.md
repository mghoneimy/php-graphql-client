# php-graphql-client
A simple PHP Graphql API client that contains a simple yet powerful GraphQL query builder and results parser to abstract the process of interacting the GraphQL APIs

# Query Example
```
$gql = (new Query('Company'))
    ->setArguments(['filter' => '{name_contains: "XD"}', 'first' => 3])
    ->setSelectionSet(
        [
            'name',
            (new Query('branches'))
                ->setArguments(['first' => 1])
                ->setSelectionSet(
                    [
                        'address',
                        (new Query('contracts'))
                            ->setArguments(['first' => 3])
                            ->setSelectionSet(['date'])
                    ]
                )
        ]
    );
```
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
# Supported Feature
- Query builder
- Results parsing in multiple formats
- Reporting API errors

# Feature on-Track
- Mutation types
- Query and field aliases
- Fragment types

# Futuristic Features
- Schema Traversal

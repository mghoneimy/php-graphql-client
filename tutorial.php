<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/6/18
 * Time: 11:55 PM
 */

require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Exception\QueryError;
use GraphQL\Query;

// Create Client object to contact the GraphQL endpoint
$client = new \GraphQL\Client(
    'http://testapi/graphql',
    ['Authorization' => 'Basic gibberish']
);


// Create the GraphQL query
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

// Run query to get results
try {
    $results = $client->runQuery($gql);
}
catch (QueryError $exception) {

    // Catch query error and desplay error details
    print_r($exception->getErrorDetails());
    exit;
}

// Display part of the returned results of the object
var_dump($results->getData()->Company[0]);

// Reformat the results to an array and get the results of part of the array
$results->reformatResults(true);
var_dump($results->getData()['Company'][1]);

// Display original response from endpoint
var_dump($results->getResponseObject());
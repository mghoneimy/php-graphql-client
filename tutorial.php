<?php

require 'GraphQL/Client.php';
require 'GraphQL/Query.php';
require 'GraphQL/Results.php';

use GraphQL\Query;

/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/6/18
 * Time: 11:55 PM
 */

// Create Client object to contact the GraphQL endpoint
$client = new \GraphQL\Client(
    'http://testapi/graphql',
    ['Authorization' => 'Basic gibberish']
);


// Create the GraphQL query
$gql = (new Query('Company'))
    ->setConstraints(['filter' => '{name_contains: "XD"}', 'first' => 3])
    ->setReturnAttributes(
        [
            'name',
            (new Query('branches'))
                ->setConstraints(['first' => 1])
                ->setReturnAttributes(
                    [
                        'address',
                        (new Query('contracts'))
                            ->setConstraints(['first' => 3])
                            ->setReturnAttributes(['date'])
                    ]
                )
        ]
    );

// Run query to get results
$results = $client->runQuery($gql);

// Display part of the returned results of the object
var_dump($results->getData()->Company[0]);

// Reformat the results to an array and get the results of part of the array
$results->reformatResults(true);
var_dump($results->getData()['Company'][1]);

// Display original response from endpoint
var_dump($results->getResponseObject());
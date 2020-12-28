<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\Mutation;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class MutationTest extends TestCase
{
    public function testMutationWithoutOperationType()
    {
        $mutation = new Mutation('createObject');

        $this->assertSame(
            'mutation {
createObject
}',
            (string) $mutation
        );
    }

    public function testMutationWithOperationType()
    {
        $mutation = new Mutation();
        $mutation
            ->setSelectionSet(
                [
                    (new Mutation('createObject'))
                        ->setArguments(['name' => 'TestObject']),
                ]
            );

        $this->assertSame(
            'mutation {
createObject(name: "TestObject")
}',
            (string) $mutation
        );
    }

    public function testMutationWithoutSelectedFields()
    {
        $mutation = (new Mutation('createObject'))
            ->setArguments(['name' => 'TestObject', 'type' => 'TestType']);
        $this->assertSame(
            'mutation {
createObject(name: "TestObject" type: "TestType")
}',
            (string) $mutation
        );
    }

    public function testMutationWithFields()
    {
        $mutation = (new Mutation('createObject'))
            ->setSelectionSet(
                [
                    'fieldOne',
                    'fieldTwo',
                ]
            );

        $this->assertSame(
            'mutation {
createObject {
fieldOne
fieldTwo
}
}',
            (string) $mutation
        );
    }

    public function testMutationWithArgumentsAndFields()
    {
        $mutation = (new Mutation('createObject'))
            ->setSelectionSet(
                [
                    'fieldOne',
                    'fieldTwo',
                ]
            )->setArguments(
                [
                    'argOne' => 1,
                    'argTwo' => 'val',
                ]
            );

        $this->assertSame(
            'mutation {
createObject(argOne: 1 argTwo: "val") {
fieldOne
fieldTwo
}
}',
            (string) $mutation
        );
    }
}

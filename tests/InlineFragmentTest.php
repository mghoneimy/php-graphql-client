<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\InlineFragment;
use GraphQL\Query;
use PHPUnit\Framework\TestCase;

/**
 * Class InlineFragmentTest.
 *
 * @coversNothing
 */
class InlineFragmentTest extends TestCase
{
    /**
     * @covers \GraphQL\InlineFragment::__construct
     * @covers \GraphQL\InlineFragment::setSelectionSet
     * @covers \GraphQL\InlineFragment::constructSelectionSet
     * @covers \GraphQL\InlineFragment::__toString
     */
    public function testConvertToString()
    {
        $fragment = new InlineFragment('Test');
        $fragment->setSelectionSet(
            [
                'field1',
                'field2',
            ]
        );

        $this->assertSame(
            '... on Test {
field1
field2
}',
            (string) $fragment
        );
    }

    /**
     * @covers \GraphQL\InlineFragment::__construct
     * @covers \GraphQL\InlineFragment::setSelectionSet
     * @covers \GraphQL\InlineFragment::constructSelectionSet
     * @covers \GraphQL\InlineFragment::__toString
     */
    public function testConvertNestedFragmentToString()
    {
        $fragment = new InlineFragment('Test');
        $fragment->setSelectionSet(
            [
                'field1',
                'field2',
                (new Query('sub_field'))
                    ->setArguments(
                        [
                            'first' => 5,
                        ]
                    )
                    ->setSelectionSet(
                        [
                            'sub_field3',
                            (new InlineFragment('Nested'))
                                ->setSelectionSet(
                                    [
                                        'another_field',
                                    ]
                                ),
                        ]
                    ),
            ]
        );

        $this->assertSame(
            '... on Test {
field1
field2
sub_field(first: 5) {
sub_field3
... on Nested {
another_field
}
}
}',
            (string) $fragment
        );
    }
}

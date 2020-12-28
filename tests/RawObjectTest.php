<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\RawObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class RawObjectTest extends TestCase
{
    /**
     * @covers \GraphQL\RawObject::__toString
     * @covers \GraphQL\RawObject::__construct
     */
    public function testConvertToString()
    {
        // Test convert array
        $json = new RawObject('[1, 4, "y", 6.7]');
        $this->assertSame('[1, 4, "y", 6.7]', (string) $json);

        // Test convert graphql object
        $json = new RawObject('{arr: [1, "z"], str: "val", int: 1, obj: {x: "y"}}');
        $this->assertSame('{arr: [1, "z"], str: "val", int: 1, obj: {x: "y"}}', (string) $json);
    }
}

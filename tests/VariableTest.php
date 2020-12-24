<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\Variable;
use PHPUnit\Framework\TestCase;

/**
 * Class VariableTest.
 *
 * @coversNothing
 */
class VariableTest extends TestCase
{
    /**
     * @covers \GraphQL\Variable::__construct
     * @covers \GraphQL\Variable::__toString
     */
    public function testCreateVariable()
    {
        $variable = new Variable('var', 'String');
        $this->assertSame('$var: String', (string) $variable);
    }

    /**
     * @depends testCreateVariable
     *
     * @covers \GraphQL\Variable::__construct
     * @covers \GraphQL\Variable::__toString
     */
    public function testCreateRequiredVariable()
    {
        $variable = new Variable('var', 'String', true);
        $this->assertSame('$var: String!', (string) $variable);
    }

    /**
     * @depends testCreateRequiredVariable
     *
     * @covers \GraphQL\Variable::__construct
     * @covers \GraphQL\Variable::__toString
     */
    public function testRequiredVariableWithDefaultValueDoesNothing()
    {
        $variable = new Variable('var', 'String', true, 'def');
        $this->assertSame('$var: String!', (string) $variable);
    }

    /**
     * @depends testCreateVariable
     *
     * @covers \GraphQL\Variable::__construct
     * @covers \GraphQL\Variable::__toString
     */
    public function testOptionalVariableWithDefaultValue()
    {
        $variable = new Variable('var', 'String', false, 'def');
        $this->assertSame('$var: String="def"', (string) $variable);

        $variable = new Variable('var', 'String', false, '4');
        $this->assertSame('$var: String="4"', (string) $variable);

        $variable = new Variable('var', 'Int', false, 4);
        $this->assertSame('$var: Int=4', (string) $variable);

        $variable = new Variable('var', 'Boolean', false, true);
        $this->assertSame('$var: Boolean=true', (string) $variable);
    }
}

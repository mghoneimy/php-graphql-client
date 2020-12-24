<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Tests;

use GraphQL\Util\StringLiteralFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class StringLiteralFormatterTest.
 *
 * @coversNothing
 */
class StringLiteralFormatterTest extends TestCase
{
    /**
     * @covers \GraphQL\Util\StringLiteralFormatter::formatValueForRHS
     */
    public function testFormatForClassRHSValue()
    {
        // Null test
        $nullString = StringLiteralFormatter::formatValueForRHS(null);
        $this->assertSame('null', $nullString);

        // String tests
        $emptyString = StringLiteralFormatter::formatValueForRHS('');
        $this->assertSame('""', $emptyString);

        $formattedString = StringLiteralFormatter::formatValueForRHS('someString');
        $this->assertSame('"someString"', $formattedString);

        $formattedString = StringLiteralFormatter::formatValueForRHS('"quotedString"');
        $this->assertSame('"\"quotedString\""', $formattedString);

        $formattedString = StringLiteralFormatter::formatValueForRHS('"quotedString"');
        $this->assertSame('"\"quotedString\""', $formattedString);

        $formattedString = StringLiteralFormatter::formatValueForRHS('\'singleQuotes\'');
        $this->assertSame('"\'singleQuotes\'"', $formattedString);

        $formattedString = StringLiteralFormatter::formatValueForRHS("with \n newlines");
        $this->assertSame("\"\"\"with \n newlines\"\"\"", $formattedString);

        // Integer tests
        $integerString = StringLiteralFormatter::formatValueForRHS(25);
        $this->assertSame('25', $integerString);

        // Float tests
        $floatString = StringLiteralFormatter::formatValueForRHS(123.123);
        $this->assertSame('123.123', $floatString);

        // Bool tests
        $stringTrue = StringLiteralFormatter::formatValueForRHS(true);
        $this->assertSame('true', $stringTrue);

        $stringFalse = StringLiteralFormatter::formatValueForRHS(false);
        $this->assertSame('false', $stringFalse);
    }

    /**
     * @covers \GraphQL\Util\StringLiteralFormatter::formatArrayForGQLQuery
     */
    public function testFormatArrayForGQLQuery()
    {
        $emptyArray = [];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($emptyArray);
        $this->assertSame('[]', $stringArray);

        $oneValueArray = [1];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($oneValueArray);
        $this->assertSame('[1]', $stringArray);

        $twoValueArray = [1, 2];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($twoValueArray);
        $this->assertSame('[1, 2]', $stringArray);

        $stringArray = ['one', 'two'];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($stringArray);
        $this->assertSame('["one", "two"]', $stringArray);

        $booleanArray = [true, false];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($booleanArray);
        $this->assertSame('[true, false]', $stringArray);

        $floatArray = [1.1, 2.2];
        $stringArray = StringLiteralFormatter::formatArrayForGQLQuery($floatArray);
        $this->assertSame('[1.1, 2.2]', $stringArray);
    }

    /**
     * @covers \GraphQL\Util\StringLiteralFormatter::formatUpperCamelCase
     */
    public function testFormatUpperCamelCase()
    {
        $snakeCase = 'some_snake_case';
        $camelCase = StringLiteralFormatter::formatUpperCamelCase($snakeCase);
        $this->assertSame('SomeSnakeCase', $camelCase);

        $nonSnakeCase = 'somenonSnakeCase';
        $camelCase = StringLiteralFormatter::formatUpperCamelCase($nonSnakeCase);
        $this->assertSame('SomenonSnakeCase', $camelCase);
    }

    /**
     * @covers \GraphQL\Util\StringLiteralFormatter::formatLowerCamelCase
     */
    public function testFormatLowerCamelCase()
    {
        $snakeCase = 'some_snake_case';
        $camelCase = StringLiteralFormatter::formatLowerCamelCase($snakeCase);
        $this->assertSame('someSnakeCase', $camelCase);

        $nonSnakeCase = 'somenonSnakeCase';
        $camelCase = StringLiteralFormatter::formatLowerCamelCase($nonSnakeCase);
        $this->assertSame('somenonSnakeCase', $camelCase);
    }
}

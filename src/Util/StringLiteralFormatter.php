<?php

declare(strict_types=1);

/*
 * This file is part of gmostafa/php-graphql-client created by Mostafa Ghoneimy<emostafagh@gmail.com>
 * For the information of copyright and license you should read the file LICENSE which is
 * distributed with this source code. For more information, see <https://packagist.org/packages/gmostafa/php-graphql-client>
 */

namespace GraphQL\Util;

/**
 * Class StringLiteralFormatter.
 */
class StringLiteralFormatter
{
    /**
     * Converts the value provided to the equivalent RHS value to be put in a file declaration.
     *
     * @param bool|float|int|string $value
     */
    public static function formatValueForRHS($value): string
    {
        if (\is_string($value)) {
            // Do not treat value as a string if it starts with '$', which indicates that it's a variable name
            if (0 !== mb_strpos($value, '$')) {
                $value = str_replace('"', '\"', $value);
                if (false !== mb_strpos($value, "\n")) {
                    $value = '"""'.$value.'"""';
                } else {
                    $value = "\"${value}\"";
                }
            }
        } elseif (\is_bool($value)) {
            if ($value) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        } elseif (null === $value) {
            $value = 'null';
        } else {
            $value = (string) $value;
        }

        return $value;
    }

    public static function formatArrayForGQLQuery(array $array): string
    {
        $arrString = '[';
        $first = true;
        foreach ($array as $element) {
            if ($first) {
                $first = false;
            } else {
                $arrString .= ', ';
            }
            $arrString .= self::formatValueForRHS($element);
        }
        $arrString .= ']';

        return $arrString;
    }

    public static function formatUpperCamelCase(string $stringValue): string
    {
        if (false === mb_strpos($stringValue, '_')) {
            return ucfirst($stringValue);
        }

        return str_replace('_', '', ucwords($stringValue, '_'));
    }

    public static function formatLowerCamelCase(string $stringValue): string
    {
        return lcfirst(static::formatUpperCamelCase($stringValue));
    }
}

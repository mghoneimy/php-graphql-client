<?php

namespace GraphQL\Util;

/**
 * Class StringLiteralFormatter
 *
 * @package GraphQL\Util
 */
class StringLiteralFormatter
{
    /**
     * Converts the value provided to the equivalent RHS value to be put in a file declaration
     *
     * @param string|int|float|bool $value
     *
     * @return string
     */
    public static function formatValueForRHS($value): string
    {
        if (is_string($value)) {
            // Do not treat value as a string if it starts with '$', which indicates that it's a variable name
            if (strpos($value, '$') !== 0) {
                $value = str_replace('"', '\"', $value);
                if (strpos($value, "\n") !== false) {
                    $value = '"""' . $value . '"""';
                } else {
                    $value = "\"$value\"";
                }
            }
        } elseif (is_bool($value)) {
            if ($value) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        } elseif ($value === null) {
            $value = 'null';
        } else {
            $value = (string) $value;
        }

        return $value;
    }

    /**
     * @param array $array
     *
     * @return string
     */
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
            $arrString .= StringLiteralFormatter::formatValueForRHS($element);
        }
        $arrString .= ']';

        return $arrString;
    }

    /**
     * @param string $stringValue
     *
     * @return string
     */
    public static function formatUpperCamelCase(string $stringValue): string
    {
        if (strpos($stringValue, '_') === false) {
            return ucfirst($stringValue);
        }

        return str_replace('_', '', ucwords($stringValue, '_'));
    }

    /**
     * @param string $stringValue
     *
     * @return string
     */
    public static function formatLowerCamelCase(string $stringValue): string
    {
        return lcfirst(static::formatUpperCamelCase($stringValue));
    }
}

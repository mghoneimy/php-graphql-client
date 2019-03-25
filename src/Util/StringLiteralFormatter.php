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
     * @param string|int|float|bool $value
     *
     * @return string
     */
    public static function formatLiteralForClass($value): string
    {
        if (is_string($value)) {
            $value = "'$value'";
        } elseif (is_bool($value)) {
            if ($value) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        } elseif (!is_null($value)) {
            $value = (string) $value;
        }

        return $value;
    }

    /**
     * @param string|int|float|bool $value
     *
     * @return string
     */
    public static function formatLiteralForGQLQuery($value): string
    {
        if (is_string($value)) {
            if ($value[0] != '"') {
                $value = '"' . $value;
            }
            if (substr($value, -1) != '"') {
                $value .= '"';
            }
        } elseif (is_bool($value)) {
            if ($value) {
                $value = 'true';
            } else {
                $value = 'false';
            }
        } elseif (!is_null($value)) {
            $value = (string) $value;
        }

        return (string) $value;
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
            $arrString .= StringLiteralFormatter::formatLiteralForGQLQuery($element);
        }
        $arrString .= ']';

        return $arrString;
    }
}
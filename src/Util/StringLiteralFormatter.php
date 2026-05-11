<?php

namespace GraphQL\Util;

/**
 * Class StringLiteralFormatter
 *
 * @package GraphQL\Util
 */
class StringLiteralFormatter
{
    const ESCAPE_SEQUENCES = [
        '\\u0000', '\\u0001', '\\u0002', '\\u0003', '\\u0004', '\\u0005', '\\u0006', '\\u0007',
        '\\b',     '\\t',     '\\n',     '\\u000B', '\\f',     '\\r',     '\\u000E', '\\u000F',
        '\\u0010', '\\u0011', '\\u0012', '\\u0013', '\\u0014', '\\u0015', '\\u0016', '\\u0017',
        '\\u0018', '\\u0019', '\\u001A', '\\u001B', '\\u001C', '\\u001D', '\\u001E', '\\u001F',
        '',        '',        '\\"',     '',        '',        '',        '',        '',
        '',        '',        '',        '',        '',        '',        '',        '', // 2F
        '',        '',        '',        '',        '',        '',        '',        '',
        '',        '',        '',        '',        '',        '',        '',        '', // 3F
        '',        '',        '',        '',        '',        '',        '',        '',
        '',        '',        '',        '',        '',        '',        '',        '', // 4F
        '',        '',        '',        '',        '',        '',        '',        '',
        '',        '',        '',        '',        '\\\\',    '',        '',        '', // 5F
        '',        '',        '',        '',        '',        '',        '',        '',
        '',        '',        '',        '',        '',        '',        '',        '', // 6F
        '',        '',        '',        '',        '',        '',        '',        '',
        '',        '',        '',        '',        '',        '',        '',        '\\u007F',
        '\\u0080', '\\u0081', '\\u0082', '\\u0083', '\\u0084', '\\u0085', '\\u0086', '\\u0087',
        '\\u0088', '\\u0089', '\\u008A', '\\u008B', '\\u008C', '\\u008D', '\\u008E', '\\u008F',
        '\\u0090', '\\u0091', '\\u0092', '\\u0093', '\\u0094', '\\u0095', '\\u0096', '\\u0097',
        '\\u0098', '\\u0099', '\\u009A', '\\u009B', '\\u009C', '\\u009D', '\\u009E', '\\u009F',
    ];

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
            if (!static::isVariable($value)) {
                if (strpos($value, "\n") !== false) {
                    $value = '"""' . $value . '"""';
                } else {
                    $value = preg_replace_callback('/[\x00-\x1f\x22\x5c\x7f-\x9f]/u', function(array $matches) {
                        $str = $matches[0];
                        return self::ESCAPE_SEQUENCES[ord($str[0])];
                    }, $value);
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
     * Treat string value as variable if it matches variable regex
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isVariable(string $value): bool {
        return preg_match('/^\$[_A-Za-z][_0-9A-Za-z]*$/', $value);
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

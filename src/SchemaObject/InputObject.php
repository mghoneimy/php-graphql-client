<?php

namespace GraphQL\SchemaObject;

use GraphQL\RawObject;
use GraphQL\Util\StringLiteralFormatter;

/**
 * Class InputObject
 *
 * @package GraphQL\SchemaObject
 */
abstract class InputObject
{
    /**
     * @return string
     */
    public function __toString()
    {
        $objectString = '{';
        $first = true;

        // TODO: Merge this code block with Query::constructArguments
        foreach ($this as $name => $value) {
            if (empty($value)) continue;

            // Append space at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $objectString .= ', ';
            }

            // Convert argument values to graphql string literal equivalent
            if (is_scalar($value)) {
                // Convert scalar value to its literal in graphql
                $value = StringLiteralFormatter::formatValueForRHS($value);
            } elseif (is_array($value)) {
                // Convert PHP array to its array representation in graphql arguments
                $value = StringLiteralFormatter::formatArrayForGQLQuery($value);
            }
            // TODO: Handle cases where a non-string-convertible object is added to the arguments
            $objectString .= $name . ': ' . $value;
        }
        $objectString .= '}';

        return $objectString;
    }

    /**
     * @return RawObject
     */
    public function toRawObject(): RawObject
    {
        return new RawObject((string) $this);
    }
}
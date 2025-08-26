<?php

namespace GraphQL;

use GraphQL\Util\StringLiteralFormatter;

class ArgumentObject
{

    /**
     * @var array
     */
    private $input;

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }


    public function __toString(): string
    {
        $result = [];
        foreach ($this->input as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $value = StringLiteralFormatter::formatValueForRHS($value);
            } elseif (is_array($value)) {
                $value = StringLiteralFormatter::formatArrayForGQLQuery($value);
            }

            $result[] = "${key}: ${value}";
        }

        return '{' . implode(', ', $result) . '}';
    }
}

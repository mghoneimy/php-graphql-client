<?php

namespace GraphQL\SchemaObject;

/**
 * Class ArgumentsObject
 *
 * @package GraphQL\SchemaObject
 */
abstract class ArgumentsObject
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $argsArray = [];
        foreach ($this as $name => $value) {
            $argsArray[$name] = $value;
        }

        return $argsArray;
    }
}
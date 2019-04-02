<?php

namespace GraphQL\SchemaObject;

/**
 * Class ArgumentsMap
 *
 * @package GraphQL\SchemaObject
 */
abstract class ArgumentsMap
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
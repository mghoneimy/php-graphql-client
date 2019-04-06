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
            if ($value !== null) {
                $argsArray[$name] = $value;
            }
        }

        return $argsArray;
    }
}
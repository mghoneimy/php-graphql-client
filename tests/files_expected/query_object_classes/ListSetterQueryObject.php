<?php

namespace GraphQL\SchemaObject;

class ListSetterQueryObject extends QueryObject
{
    const OBJECT_NAME = "ListSetter";

    public function setNames(array $names)
    {
        $this->names = $names;
    
        return $this;
    }
}
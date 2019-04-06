<?php

namespace GraphQL\SchemaObject;

class WithSetterQueryObject extends QueryObject
{
    const OBJECT_NAME = "WithSetter";

    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }
}
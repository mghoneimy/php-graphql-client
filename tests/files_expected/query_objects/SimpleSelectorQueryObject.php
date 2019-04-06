<?php

namespace GraphQL\SchemaObject;

class SimpleSelectorQueryObject extends QueryObject
{
    const OBJECT_NAME = "SimpleSelector";

    public function selectName()
    {
        $this->selectField("name");
    
        return $this;
    }
}
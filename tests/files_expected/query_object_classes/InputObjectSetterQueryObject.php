<?php

namespace GraphQL\SchemaObject;

class InputObjectSetterQueryObject extends QueryObject
{
    const OBJECT_NAME = "InputObjectSetter";

    public function setFilterBy(_TestFilter $testFilter)
    {
        $this->filterBy = $testFilter;
    
        return $this;
    }
}
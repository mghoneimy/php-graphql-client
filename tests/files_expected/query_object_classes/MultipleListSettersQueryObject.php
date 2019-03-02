<?php

namespace GraphQL\SchemaObject;

class MultipleListSettersQueryObject extends QueryObject
{
    const OBJECT_NAME = 'MultipleListSetters';

    public function setLastNames(array $lastNames)
    {
        $this->last_names = $lastNames;
    
        return $this;
    }

    public function setFirstNames(array $firstNames)
    {
        $this->firstNames = $firstNames;
    
        return $this;
    }
}
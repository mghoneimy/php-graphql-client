<?php

namespace GraphQL\SchemaObject;

class WithScalarArgArgumentsObject extends ArgumentsObject
{
    protected $scalarProperty;

    public function setScalarProperty($scalarProperty)
    {
        $this->scalarProperty = $scalarProperty;
    
        return $this;
    }
}
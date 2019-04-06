<?php

namespace GraphQL\SchemaObject;

class WithInputObjectArgArgumentsObject extends ArgumentsObject
{
    protected $objectProperty;

    public function setObjectProperty(SomeInputObject $someInputObject)
    {
        $this->objectProperty = $someInputObject;
    
        return $this;
    }
}
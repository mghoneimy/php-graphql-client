<?php

namespace GraphQL\SchemaObject;

class WithInputObjectValueInputObject extends InputObject
{
    protected $inputObject;

    public function setInputObject(WithListValueInputObject $withListValueInputObject)
    {
        $this->inputObject = $withListValueInputObject;
    
        return $this;
    }
}
<?php

namespace GraphQL\SchemaObject;

class WithMultipleInputObjectArgsArgumentsObject extends ArgumentsObject
{
    protected $objectProperty;
    protected $another_object_property;

    public function setObjectProperty(SomeInputObject $someInputObject)
    {
        $this->objectProperty = $someInputObject;
    
        return $this;
    }

    public function setAnotherObjectProperty(AnotherInputObject $anotherInputObject)
    {
        $this->another_object_property = $anotherInputObject;
    
        return $this;
    }
}
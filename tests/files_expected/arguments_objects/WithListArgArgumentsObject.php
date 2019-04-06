<?php

namespace GraphQL\SchemaObject;

class WithListArgArgumentsObject extends ArgumentsObject
{
    protected $listProperty;

    public function setListProperty(array $listProperty)
    {
        $this->listProperty = $listProperty;
    
        return $this;
    }
}
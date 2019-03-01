<?php

namespace GraphQL\SchemaObject;

class WithListValueInputObject extends InputObject
{
    protected $listOne;

    public function setListOne(array $listOne)
    {
        $this->listOne = $listOne;
    
        return $this;
    }
}
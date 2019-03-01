<?php

namespace GraphQL\SchemaObject;

class WithScalarValueInputObject extends InputObject
{
    protected $valOne;

    public function setValOne($valOne)
    {
        $this->valOne = $valOne;
    
        return $this;
    }
}
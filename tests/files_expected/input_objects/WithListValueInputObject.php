<?php

namespace GraphQL\Tests\SchemaObject;

use GraphQL\SchemaObject\InputObject;

class WithListValueInputObject extends InputObject
{
    protected $listOne;

    public function setListOne(array $listOne)
    {
        $this->listOne = $listOne;
    
        return $this;
    }
}
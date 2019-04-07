<?php

namespace GraphQL\Tests\SchemaObject;

use GraphQL\SchemaObject\InputObject;

class WithMultipleListValuesInputObject extends InputObject
{
    protected $listOne;
    protected $list_two;

    public function setListOne(array $listOne)
    {
        $this->listOne = $listOne;
    
        return $this;
    }

    public function setListTwo(array $listTwo)
    {
        $this->list_two = $listTwo;
    
        return $this;
    }
}
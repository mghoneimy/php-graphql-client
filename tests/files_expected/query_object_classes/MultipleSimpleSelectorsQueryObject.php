<?php

namespace GraphQL\SchemaObject;

class MultipleSimpleSelectorsQueryObject extends QueryObject
{
    use MultipleSimpleSelectorsTrait;

    const OBJECT_NAME = 'MultipleSimpleSelectors';

    public function selectFirstName()
    {
        $this->selectField('first_name');
    
        return $this;
    }

    public function selectLastName()
    {
        $this->selectField('last_name');
    
        return $this;
    }
}
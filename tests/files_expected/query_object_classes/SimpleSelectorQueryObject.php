<?php

namespace GraphQL\SchemaObject;

class SimpleSelectorQueryObject extends QueryObject
{
    use SimpleSelectorTrait;

    const OBJECT_NAME = 'SimpleSelector';

    public function selectName()
    {
        $this->selectField('name');
    
        return $this;
    }
}
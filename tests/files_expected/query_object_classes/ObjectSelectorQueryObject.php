<?php

namespace GraphQL\SchemaObject;

class ObjectSelectorQueryObject extends QueryObject
{
    use ObjectSelectorTrait;

    const OBJECT_NAME = 'ObjectSelector';

    public function selectOthers()
    {
        $object = new OtherQueryObject('others');
        $this->selectField($object);
    
        return $object;
    }
}
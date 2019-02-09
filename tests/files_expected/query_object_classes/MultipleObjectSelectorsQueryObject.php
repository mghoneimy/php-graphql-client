<?php

namespace GraphQL\SchemaObject;

class MultipleObjectSelectorsQueryObject extends QueryObject
{
    use MultipleObjectSelectorsTrait;

    const OBJECT_NAME = 'MultipleObjectSelectors';

    public function selectRightObjects()
    {
        $object = new RightObjectQueryObject('right_objects');
        $this->selectField($object);
    
        return $object;
    }

    public function selectLeftObjects()
    {
        $object = new LeftObjectQueryObject('left_objects');
        $this->selectField($object);
    
        return $object;
    }
}
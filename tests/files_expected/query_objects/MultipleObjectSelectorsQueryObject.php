<?php

namespace GraphQL\SchemaObject;

class MultipleObjectSelectorsQueryObject extends QueryObject
{
    const OBJECT_NAME = "MultipleObjectSelectors";

    public function selectRightObjects(RootRightArgumentsObject $argsObject = null)
    {
        $object = new RightQueryObject("right_objects");
        if ($argsObject !== null) {
            $object->appendArguments($argsObject->toArray());
        }
        $this->selectField($object);
    
        return $object;
    }

    public function selectLeftObjects(RootLeftArgumentsObject $argsObject = null)
    {
        $object = new LeftQueryObject("left_objects");
        if ($argsObject !== null) {
            $object->appendArguments($argsObject->toArray());
        }
        $this->selectField($object);
    
        return $object;
    }
}
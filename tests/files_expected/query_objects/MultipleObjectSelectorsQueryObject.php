<?php

namespace GraphQL\Tests\SchemaObject;

use GraphQL\SchemaObject\QueryObject;

class MultipleObjectSelectorsQueryObject extends QueryObject
{
    const OBJECT_NAME = "MultipleObjectSelectors";

    public function selectRightObjects(MultipleObjectSelectorsRightObjectsArgumentsObject $argsObject = null)
    {
        $object = new RightQueryObject("right_objects");
        if ($argsObject !== null) {
            $object->appendArguments($argsObject->toArray());
        }
        $this->selectField($object);
    
        return $object;
    }

    public function selectLeftObjects(MultipleObjectSelectorsLeftObjectsArgumentsObject $argsObject = null)
    {
        $object = new LeftQueryObject("left_objects");
        if ($argsObject !== null) {
            $object->appendArguments($argsObject->toArray());
        }
        $this->selectField($object);
    
        return $object;
    }
}
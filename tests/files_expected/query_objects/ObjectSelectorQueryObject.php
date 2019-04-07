<?php

namespace GraphQL\SchemaObject;

class ObjectSelectorQueryObject extends QueryObject
{
    const OBJECT_NAME = "ObjectSelector";

    public function selectOthers(RootOthersArgumentsObject $argsObject = null)
    {
        $object = new OtherQueryObject("others");
        if ($argsObject !== null) {
            $object->appendArguments($argsObject->toArray());
        }
        $this->selectField($object);
    
        return $object;
    }
}
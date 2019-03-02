<?php

namespace GraphQL\SchemaObject;

class OtherObjectQueryObject extends QueryObject
{
    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function selectName()
    {
        $this->selectField('name');

        return $this;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 2/10/19
 * Time: 1:35 AM
 */

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
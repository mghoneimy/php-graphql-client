<?php

namespace GraphQL\SchemaObject;

class ComplexObjectQueryObject extends QueryObject
{
    use ComplexObjectTrait;

    const OBJECT_NAME = 'ComplexObject';

    public function setId($id)
    {
        $this->_id = $id;
    
        return $this;
    }

    public function selectId()
    {
        $this->selectField('_id');
    
        return $this;
    }

    public function selectSimples()
    {
        $object = new SimpleObjectQueryObject('simples');
        $this->selectField($object);
    
        return $object;
    }

    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }

    public function selectCreationDate()
    {
        $this->selectField('creation_date');
    
        return $this;
    }
}
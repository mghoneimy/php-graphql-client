<?php

namespace GraphQL\SchemaObject;

class ComplexObjectQueryObject extends QueryObject
{
    const OBJECT_NAME = 'ComplexObject';

    protected $_id;
    protected $creation_date;

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

    public function selectCreationDate()
    {
        $this->selectField('creation_date');
    
        return $this;
    }

    public function setId($id)
    {
        $this->_id = $id;
    
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }
}
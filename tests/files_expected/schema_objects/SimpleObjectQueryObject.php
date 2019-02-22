<?php

namespace GraphQL\SchemaObject;

class SimpleObjectQueryObject extends QueryObject
{
    const OBJECT_NAME = 'SimpleObject';

    protected $_id;
    protected $name;
    protected $creation_date;

    public function selectId()
    {
        $this->selectField('_id');
    
        return $this;
    }

    public function selectName()
    {
        $this->selectField('name');
    
        return $this;
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

    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }
}
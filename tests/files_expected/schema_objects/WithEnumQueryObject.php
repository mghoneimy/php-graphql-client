<?php

namespace GraphQL\SchemaObject;

class WithEnumQueryObject extends QueryObject
{
    const OBJECT_NAME = 'WithEnum';

    protected $_id;
    protected $creation_date;
    protected $orderBy;

    public function selectId()
    {
        $this->selectField('_id');
    
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

    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }

    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;
    
        return $this;
    }
}
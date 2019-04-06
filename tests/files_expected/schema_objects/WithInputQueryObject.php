<?php

namespace GraphQL\SchemaObject;

class WithInputQueryObject extends QueryObject
{
    const OBJECT_NAME = "WithInput";

    protected $_id;
    protected $name;
    protected $filterBy;

    public function selectId()
    {
        $this->selectField("_id");
    
        return $this;
    }

    public function selectName()
    {
        $this->selectField("name");
    
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

    public function setFilterBy(_FilterByInputObject $filterByInputObject)
    {
        $this->filterBy = $filterByInputObject;
    
        return $this;
    }
}
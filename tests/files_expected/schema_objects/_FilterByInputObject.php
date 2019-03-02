<?php

namespace GraphQL\SchemaObject;

class _FilterByInputObject extends InputObject
{
    protected $name;
    protected $name_contains;
    protected $name_not;
    protected $name_in;
    protected $name_not_in;

    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    public function setNameContains($nameContains)
    {
        $this->name_contains = $nameContains;
    
        return $this;
    }

    public function setNameNot($nameNot)
    {
        $this->name_not = $nameNot;
    
        return $this;
    }

    public function setNameIn(array $nameIn)
    {
        $this->name_in = $nameIn;
    
        return $this;
    }

    public function setNameNotIn(array $nameNotIn)
    {
        $this->name_not_in = $nameNotIn;
    
        return $this;
    }
}
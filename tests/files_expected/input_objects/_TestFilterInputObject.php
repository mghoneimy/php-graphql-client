<?php

namespace GraphQL\SchemaObject;

class _TestFilterInputObject extends InputObject
{
    protected $first_name;
    protected $lastName;
    protected $ids;

    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    public function setIds(array $ids)
    {
        $this->ids = $ids;
    
        return $this;
    }
}
<?php

namespace GraphQL\SchemaObject;

class WithMultipleSettersQueryObject extends QueryObject
{
    use WithMultipleSettersTrait;

    const OBJECT_NAME = 'WithMultipleSetters';

    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }
}
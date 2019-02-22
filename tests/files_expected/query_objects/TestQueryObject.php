<?php

namespace GraphQL\SchemaObject;

class TestQueryObject extends QueryObject
{
    const OBJECT_NAME = 'Test';

    protected $property_one;
    protected $propertyTwo;

    public function setPropertyOne($propertyOne)
    {
        $this->property_one = $propertyOne;
    
        return $this;
    }

    public function selectPropertyOne()
    {
        $this->selectField('property_one');
    
        return $this;
    }

    public function setPropertyTwo($propertyTwo)
    {
        $this->propertyTwo = $propertyTwo;
    
        return $this;
    }

    public function selectPropertyTwo()
    {
        $this->selectField('propertyTwo');
    
        return $this;
    }

    public function selectOtherObjects()
    {
        $object = new OtherObjectQueryObject('other_objects');
        $this->selectField($object);
    
        return $object;
    }
}
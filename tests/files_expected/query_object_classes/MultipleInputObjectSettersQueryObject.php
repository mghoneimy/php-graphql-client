<?php

namespace GraphQL\SchemaObject;

class MultipleInputObjectSettersQueryObject extends QueryObject
{
    const OBJECT_NAME = 'MultipleInputObjectSetters';

    public function setFilterOneBy(_TestFilterOne $testFilterOne)
    {
        $this->filter_one_by = $testFilterOne;
    
        return $this;
    }

    public function setFilterAllBy(_TestFilterAll $testFilterAll)
    {
        $this->filterAllBy = $testFilterAll;
    
        return $this;
    }
}
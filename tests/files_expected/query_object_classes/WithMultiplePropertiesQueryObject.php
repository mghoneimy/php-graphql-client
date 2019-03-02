<?php

namespace GraphQL\SchemaObject;

class WithMultiplePropertiesQueryObject extends QueryObject
{
    const OBJECT_NAME = 'WithMultipleProperties';

    protected $first_property;
    protected $secondProperty;
}
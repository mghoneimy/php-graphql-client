<?php

namespace GraphQL\Tests\SchemaObject;

use GraphQL\SchemaObject\EnumObject;

class WithMultipleConstantsEnumObject extends EnumObject
{
    const SOME_VALUE = "some_value";
    const ANOTHER_VALUE = "another_value";
    const ONEMOREVALUE = "oneMoreValue";
}
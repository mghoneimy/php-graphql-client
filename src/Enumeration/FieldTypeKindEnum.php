<?php

namespace GraphQL\Enumeration;

/**
 * Class FieldTypeKindEnum
 *
 * @package GraphQL\Enumeration
 */
class FieldTypeKindEnum
{
    const SCALAR       = 'SCALAR';
    const LIST         = 'LIST';
    const NON_NULL     = 'NON_NULL';
    const OBJECT       = 'OBJECT';
    const INPUT_OBJECT = 'INPUT_OBJECT';
    const ENUM_OBJECT  = 'ENUM';
}